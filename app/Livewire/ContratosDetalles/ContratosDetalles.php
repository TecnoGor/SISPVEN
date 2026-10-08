<?php

namespace App\Livewire\ContratosDetalles;

use Carbon\Carbon;
use App\Models\Envio;
use Livewire\Component;
use App\Models\TipoPago;
use Livewire\Attributes\Layout;
use App\Models\ClienteCorporativo;
use App\Models\ContratoCorporativo;
use App\Models\FacturacionContrato;
use App\Models\ContratoCorporativoDetalle;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class ContratosDetalles extends Component
{
    public $cliente, $detalle_especifico, $metodo_pago_seleccionado, $contrato_id, $tarifa_nueva;

    public $monto;
    public $monto_pagado = 0;
    public $modal_open = false;
    public $modal_open2 = false;
    public $modal_open3 = false;
    public $modal_open4 = false;
    public $usuario = [];
    public $contratos = [];
    public $clientes = [];
    public $detalles = [];
    public $metodos_pago = [];
    public $pagos = [];
    public $tarifa = [];
    /** @var \Illuminate\Support\Collection|\App\Models\Envio[] */
    public $envios = [];

    // ── Datos congelados al abrir el modal de pago ──
    public $tasa_aplicada;     // tasa divisa→Bs en el momento de abrir el modal
    public $cuota_divisa;      // monto de la cuota en la divisa del contrato
    public $cuota_bs;          // monto equivalente en Bs (lo que paga el cliente)
    public $divisa_nombre;     // nombre de la divisa (USD, EUR, etc.)


    public function mount()
    {
        $this->usuario = auth()->user();
        $this->clientes = ClienteCorporativo::all();
        $this->metodos_pago = TipoPago::all();
    }


    public function verDetalles($contrato_id)
    {
        $this->contrato_id = $contrato_id;
        $this->modal_open = true;
    }

    public function verEnvios($contrato_id)
    {
        $this->contrato_id = $contrato_id;
        $this->envios = Envio::where('contrato_corporativo_id', $this->contrato_id)->orderBy('created_at', 'asc')->get();

        if ($this->envios->isNotEmpty()) {
            $this->modal_open4 = true;
        } else {
            $this->dispatch('alertSuccess3', message: 'El cliente aun no posee envios realizados con este contrato');
            return;
        }
    }

    public function cerrarCuotas()
    {
        $this->modal_open = false;
        $this->modal_open4 = false;
    }

    public function cerrarPago()
    {
        $this->modal_open2 = false;
        $this->monto_pagado = 0;
        $this->pagos = [];
        $this->tasa_aplicada = null;
        $this->cuota_divisa = null;
        $this->cuota_bs = null;
        $this->divisa_nombre = null;
    }

    public function cerrar_tarifa()
    {
        $this->modal_open3 = false;
    }

    public function pagar($contrato_detalle)
    {
        $this->detalle_especifico = ContratoCorporativoDetalle::with('contrato.divisa')
            ->where('contrato_corporativo_detalle_id', $contrato_detalle)
            ->first();

        if (!$this->detalle_especifico || !$this->detalle_especifico->contrato) {
            $this->dispatch('alertSuccess2', message: 'No se pudo cargar el detalle del contrato.');
            return;
        }

        $contrato = $this->detalle_especifico->contrato;

        // Validar que el contrato tenga divisa asignada
        if (!$contrato->parametro_id || !$contrato->divisa) {
            $this->dispatch('alertSuccess2', message: 'El contrato no tiene una divisa válida asignada.');
            return;
        }

        $tasa = $contrato->divisa->valor;

        if (!$tasa || $tasa <= 0) {
            $this->dispatch('alertSuccess2', message: 'La tasa de la divisa no es válida. Contacte al administrador.');
            return;
        }

        // Congelar los valores en el estado del componente
        $this->tasa_aplicada = $tasa;
        $this->divisa_nombre = $contrato->divisa->nombre;
        $this->cuota_divisa = $this->detalle_especifico->cuota;
        $this->cuota_bs = bcmul((string)$this->cuota_divisa, (string)$tasa, 2);

        $this->modal_open2 = true;
    }

    public function aprobar_pago()
    {
        if ($this->monto_pagado >= $this->cuota_bs) {
            $this->actualizar_contrato($this->detalle_especifico);
        } else {
            $this->dispatch(
                'alertSuccess2',
                message: 'El monto pagado debe ser igual o mayor a Bs ' . number_format($this->cuota_bs, 2, ',', '.')
            );
        }
    }

    public function modificar_tarifa($tarifa)
    {
        $this->modal_open3 = true;
        $this->tarifa = ContratoCorporativoDetalle::where('contrato_corporativo_detalle_id', $tarifa)->first();
        $this->tarifa_nueva = $this->tarifa['cuota'];
    }

    public function cambiar_tarifa()
    {
        // Validar los campos
        $this->validate([
            'tarifa_nueva' => 'required|regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/',
        ]);

        // Convertir el monto a formato numérico (quitar formato de texto)
        $tarifa_n = floatval(str_replace(',', '.', str_replace('.', '', $this->tarifa_nueva)));

        $cuota_cambiar = ContratoCorporativoDetalle::where('contrato_corporativo_detalle_id', $this->tarifa['contrato_corporativo_detalle_id'])->first();
        $cuota_cambiar->cuota = $tarifa_n;
        $cuota_cambiar->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se modificó la tarifa de la cuota de contrato ({$cuota_cambiar->contrato_corporativo_detalle_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Cuota modificada correctamente');

        $this->modal_open3 = false;
    }

    private function actualizar_contrato(ContratoCorporativoDetalle $detalle)
    {
        $detalle->cancelada = true;
        $detalle->fecha_cancelada = Carbon::now()->format('Y-m-d');
        $detalle->tasa_pago = $this->tasa_aplicada;
        $detalle->monto_bs_pagado = $this->cuota_bs;
        $detalle->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se aprobó el pago de la cuota de contrato corporativo ({$detalle->contrato_corporativo_detalle_id}) del contrato ({$this->contrato_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Cuota pagada correctamente!');

        foreach ($this->pagos as $pago) {
            FacturacionContrato::create([
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'contrato_corporativo_id' => $this->contrato_id,
                'contrato_corporativo_detalle_id' => $detalle->contrato_corporativo_detalle_id,
                'tipo_pago_id' => $pago['tipo_pago_id'],
                'monto' => $pago['monto'],
                'referencia' => $pago['referencia'] ?? null,
                'tasa_aplicada' => $this->tasa_aplicada,
                'monto_divisa' => $this->tasa_aplicada > 0
                    ? bcdiv((string)$pago['monto'], (string)$this->tasa_aplicada, 2)
                    : null,
            ]);
        }

        $this->cerrarPago();
    }

    public function activarContrato($contrato_id)
    {
        $confirmar = ContratoCorporativo::where('cliente_corporativo_id', $this->cliente)->where('activo', true)->first();
        if ($confirmar) {
            $this->dispatch('alertSuccess2', message: 'El cliente ya posee un contrato activo actualmente');
            return;
        } else {
            $contrato = ContratoCorporativo::where('contrato_corporativo_id', $contrato_id)->first();

            $contrato->activo = true;
            $contrato->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se reactivó el contrato corporativo ({$contrato->contrato_corporativo_id})",
            ]);

            $this->dispatch('alertSuccess', message: 'Contrato reactivado');
        }
    }

    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }

    public function render()
    {
        if ($this->cliente) {
            $this->contratos = ContratoCorporativo::where('cliente_corporativo_id', $this->cliente)
                ->orderBy('activo', 'desc')->orderBy('created_at', 'desc')->get();
        } else {
            $this->contratos = [];
        }

        $this->detalles = ContratoCorporativoDetalle::where('contrato_corporativo_id', $this->contrato_id)
            ->orderBy('cancelada', 'asc')->orderBy('fecha_limite', 'asc')->get();

        return view('livewire.contratos-detalles.contratos-detalles');
    }
}
