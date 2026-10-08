<?php

namespace App\Livewire\Oficina;

use App\Models\NumeroDespachoOficina;
use App\Models\Saca;
use App\Models\UsuarioSeguimiento;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PaquetesManifiestos extends Component
{
    // El parametro de ruta es el numero_despacho_id: la guia ahora se guia por el
    // DESPACHO, no por el manifiesto. Cada despacho agrupa las valijas de un par
    // (origen, destino) y puede imprimirse desde su creacion, sin esperar la salida.
    public $numero_despacho_id;

    /** Muestra el aviso previo a confirmar la guia (accion irreversible). */
    public $mostrarAvisoConfirmar = false;

    /**
     * Manifiestos del despacho historico, cuando hay mas de uno y el operador
     * debe elegir. Vacio en el flujo normal.
     */
    public $manifiestosHistoricos = [];

    /**
     * Un despacho SIN `oficina_destino_id` es del modelo anterior: el correlativo
     * era por oficina de origen y agrupaba valijas hacia rutas distintas, asi que
     * su guia no se puede reconstruir desde `sacas` (mezclaria envios que nunca
     * viajaron juntos). Su informacion real esta en los manifiestos.
     *
     * Ver docs/notas/plan-guias-historicas-por-manifiesto.md
     */
    public function mount()
    {
        $despacho = NumeroDespachoOficina::find($this->numero_despacho_id);

        if (!$despacho || $despacho->oficina_destino_id !== null) {
            return; // Despacho del modelo actual: flujo normal.
        }

        $manifiestos = $this->manifiestosDelDespacho();

        // Sin manifiesto no hubo salida: no hay guia historica que mostrar.
        if ($manifiestos->isEmpty()) {
            session()->flash('error', 'Este despacho es del modelo anterior y no llegó a despacharse, por lo que no tiene guía.');
            return redirect()->route('manifiestos');
        }

        // Caso mayoritario (6 de cada 10): un solo manifiesto, se va directo.
        if ($manifiestos->count() === 1) {
            return redirect()->route('guia-manifiesto', $manifiestos->first()->manifiesto_id);
        }

        // Varios: la vista muestra la pantalla de seleccion.
        $this->manifiestosHistoricos = $manifiestos->all();
    }

    /**
     * Manifiestos en los que figuran las valijas de este despacho. Un despacho
     * viejo abarca varias salidas fisicas, de ahi que puedan ser varios.
     */
    private function manifiestosDelDespacho()
    {
        return \App\Models\Manifiesto::with('oficinaDestino')
            ->whereIn('manifiesto_id', function ($q) {
                $q->select('mp.manifiesto_id')
                    ->from('manifiestos_paquetes as mp')
                    ->join('sacas as s', 's.saca_id', '=', 'mp.saca_id')
                    ->where('s.numero_despacho_id', $this->numero_despacho_id);
            })
            ->withCount('paquetes')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Cierra el despacho (activo = false). Equivale a "confirmar" la guia: a partir
     * de aqui ese correlativo queda despachado y una valija nueva al mismo destino
     * abrira otro despacho (otra guia). El despacho tampoco admitira mas envios al
     * descubierto (ver el guard en RegistrarSalidaCOP/OPT::asociarEnvioADespacho).
     */
    public function confirmarGuia()
    {
        $this->mostrarAvisoConfirmar = false;

        $despacho = NumeroDespachoOficina::find($this->numero_despacho_id);

        // Puede haberse confirmado ya desde otra pestaña: no se vuelve a cerrar ni
        // se registra un seguimiento duplicado.
        if ($despacho && !$despacho->activo) {
            session()->flash('error', 'Esta guía ya fue confirmada.');
            return redirect()->route('manifiestos');
        }

        if ($despacho) {
            $despacho->activo = false;
            $despacho->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se confirmó la guía del despacho N° {$despacho->numero_despacho} ({$this->numero_despacho_id})",
            ]);

            return redirect()->route('manifiestos');
        }

        session()->flash('error', 'El despacho no fue encontrado.');
    }

    /**
     * Vuelve a abrir un despacho cuya guia se confirmo por error o al que hay que
     * agregar (o quitar) contenido antes de despacharlo.
     *
     * Solo mientras NO haya salido: si alguna de sus valijas ya figura en un
     * manifiesto, el despacho viajo y su guia es historica. Reabrirlo permitiria
     * agregar valijas a un despacho ya emitido y la guia dejaria de coincidir con
     * lo que realmente salio.
     */
    public function reabrirGuia()
    {
        $despacho = NumeroDespachoOficina::find($this->numero_despacho_id);

        if (!$despacho) {
            session()->flash('error', 'El despacho no fue encontrado.');
            return;
        }

        if ($despacho->activo) {
            $this->dispatch('alertError', message: 'Esta guía ya está abierta.');
            return;
        }

        if ($this->despachoYaSalio()) {
            $this->dispatch('alertError', message: 'Este despacho ya fue despachado: su guía no puede reabrirse.');
            return;
        }

        // Solo puede haber UN despacho abierto por par (origen, destino) — hay un
        // indice unico parcial que lo garantiza. Si ya se abrio otro hacia el mismo
        // destino, hay que despachar ese primero: reabrir aqui violaria el indice.
        $otroAbierto = NumeroDespachoOficina::where('oficina_id', $despacho->oficina_id)
            ->where('oficina_destino_id', $despacho->oficina_destino_id)
            ->where('activo', true)
            ->first();

        if ($otroAbierto) {
            $this->dispatch('alertError', message: "Ya hay otro despacho abierto hacia ese destino (N° {$otroAbierto->numero_despacho}). Despáchelo antes de reabrir esta guía.");
            return;
        }

        $despacho->activo = true;
        $despacho->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se reabrió la guía del despacho N° {$despacho->numero_despacho} ({$this->numero_despacho_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Guía reabierta: ya puede agregar o quitar valijas y envíos.');
    }

    /**
     * El despacho salio si alguna de sus valijas figura en un manifiesto. Es la
     * misma señal que usa la eliminacion de valijas en RegistrarSalidaCOP/OPT.
     */
    protected function despachoYaSalio(): bool
    {
        return \App\Models\ManifiestoPaquete::whereIn('saca_id', function ($q) {
            $q->select('saca_id')
                ->from('sacas')
                ->where('numero_despacho_id', $this->numero_despacho_id);
        })->exists();
    }

    /**
     * Sacas del despacho. Incluye las valijas de peso (sin envios): se listan igual
     * porque la fuente es la propia saca, no sus envios.
     */
    protected function sacasDelDespacho()
    {
        return Saca::with(['tipoSaca', 'oficinaOrigen', 'oficinaDestino', 'numeroDespacho', 'envios'])
            ->where('numero_despacho_id', $this->numero_despacho_id)
            ->orderBy('saca_id')
            ->get();
    }

    public function render()
    {
        $despacho = NumeroDespachoOficina::with(['oficina', 'oficinaDestino'])
            ->find($this->numero_despacho_id);

        $sacas = $this->sacasDelDespacho();

        // Relacion de envios certificados: los envios contenidos en las valijas del
        // despacho. La valija de peso no aporta filas aqui (no lleva envios), pero si
        // figura en la tabla principal de la guia.
        $paquetesCertificados = collect();

        foreach ($sacas as $saca) {
            $origen  = optional($saca->oficinaOrigen)->nombre
                ?? optional($despacho->oficina)->nombre;
            $destino = optional($saca->oficinaDestino)->nombre
                ?? optional($despacho->oficinaDestino)->nombre;

            foreach ($saca->envios as $envio) {
                $paquetesCertificados->push([
                    'tipo'           => 'envio',
                    'id'             => $envio->envio_id,
                    'codigo'         => $envio->codigo_envio,
                    'OficinaOrigen'  => $origen,
                    'OficinaDestino' => $destino,
                    'peso'           => $envio->peso ?? 'N/A',
                    'fecha'          => $envio->created_at->format('d/m/Y h:i A'),
                    'hora'           => $envio->created_at->format('h:i A'),
                    'precinto'       => $saca->numero_precinto,
                ]);
            }
        }

        // Envios al descubierto (sueltos) asociados a este despacho: viajan con el
        // pero sin valija. Se listan como filas propias, marcadas "Al descubierto".
        $sueltos = \App\Models\EnvioDescubiertoDespacho::with('envio')
            ->where('numero_despacho_id', $this->numero_despacho_id)
            ->get();

        $origenDespacho  = optional($despacho->oficina)->nombre;
        $destinoDespacho = optional($despacho->oficinaDestino)->nombre;

        foreach ($sueltos as $suelto) {
            $envio = $suelto->envio;
            if (!$envio) {
                continue;
            }

            $paquetesCertificados->push([
                'tipo'           => 'envio',
                'id'             => $envio->envio_id,
                'codigo'         => $envio->codigo_envio,
                'OficinaOrigen'  => $origenDespacho,
                'OficinaDestino' => $destinoDespacho,
                'peso'           => $envio->peso ?? 'N/A',
                'fecha'          => $envio->created_at->format('d/m/Y h:i A'),
                'hora'           => $envio->created_at->format('h:i A'),
                'precinto'       => 'Al descubierto',
            ]);
        }

        return view('livewire.oficina.paquetes-manifiestos', [
            'despacho' => $despacho,
            'sacas' => $sacas,
            'paquetesCertificados' => $paquetesCertificados,
            // Una guia ya despachada no puede reabrirse: su manifiesto ya se emitio.
            'despachoYaSalio' => $this->despachoYaSalio(),
        ]);
    }
}