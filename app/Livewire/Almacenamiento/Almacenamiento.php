<?php

namespace App\Livewire\Almacenamiento;
use Carbon\Carbon;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\Parametro;
use App\Models\ClienteCorporativo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\FacturacionContrato;
use App\Models\ContratoAlmacenamiento;
use App\Models\ContratoCorporativoDetalle;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class Almacenamiento extends Component
{
    public $cliente_corporativo, $metros_cuadrados, $agente_autorizado, $cuotas, $contrato_id;

    public $tarifa;
    public $tarifa_convertida;
    public $parametro_id;
    public $preview_bs = 0;

    public $detalle_id;
    public $detalle_tarifa_id;
    public $tarifa_nueva = 0;

    public $cancelar_cuota = false;
    public $perPage = 10;
    public $search;
    public $mostrar_cuotas = false;
    public $monto_pagado = 0;
    public $usuario = [];

    public $modal_crear = false;
    public $modal_pagar = false;
    public $modal_modificar_tarifa = false;
    public $ver_detalles = false;

    public $pagos = [];
    public $clientes_corporativos = [];
    public $divisas_activas = [];

    // Datos congelados al abrir el modal de pago
    public $tasa_aplicada;
    public $cuota_divisa;
    public $cuota_bs;
    public $divisa_nombre;


    public function mount()
    {
        $this->usuario = auth()->user();
        $this->clientes_corporativos = ClienteCorporativo::all();
        $this->divisas_activas = Parametro::where('activo', true)->get();
    }

    public function mostrarCuotas($contrato_id)
    {
        $this->contrato_id = $contrato_id;
        $this->ver_detalles = true;
    }

    public function pagar($contrato_detalle)
    {
        $detalle = ContratoCorporativoDetalle::with('almacenamiento.divisa')
            ->where('contrato_corporativo_detalle_id', $contrato_detalle)
            ->first();

        if (!$detalle || !$detalle->almacenamiento) {
            $this->dispatch('alertSuccess2', message: 'No se pudo cargar el detalle del contrato.');
            return;
        }

        $contrato = $detalle->almacenamiento;

        if (!$contrato->parametro_id || !$contrato->divisa) {
            $this->dispatch('alertSuccess2', message: 'El contrato no tiene una divisa válida asignada.');
            return;
        }

        $tasa = $contrato->divisa->valor;

        if (!$tasa || $tasa <= 0) {
            $this->dispatch('alertSuccess2', message: 'La tasa de la divisa no es válida. Contacte al administrador.');
            return;
        }

        $this->detalle_id = $contrato_detalle;
        $this->monto_pagado = 0;
        $this->pagos = [];
        $this->tasa_aplicada = $tasa;
        $this->divisa_nombre = $contrato->divisa->nombre;
        $this->cuota_divisa = $detalle->cuota;
        $this->cuota_bs = bcmul((string)$this->cuota_divisa, (string)$tasa, 2);
        $this->modal_pagar = true;
    }

    public function modalOpen()
    {
        $this->modal_crear = true;
    }

    public function modalClose()
    {
        $this->modal_crear = false;
        $this->ver_detalles = false;
        $this->cliente_corporativo = null;
        $this->metros_cuadrados = null;
        $this->tarifa = null;
        $this->tarifa_convertida = null;
        $this->cuotas = null;
        $this->parametro_id = null;
        $this->preview_bs = 0;
    }

    public function cerrar_pago()
    {
        $this->modal_pagar = false;
        $this->monto_pagado = 0;
        $this->pagos = [];
        $this->detalle_id = null;
        $this->tasa_aplicada = null;
        $this->cuota_divisa = null;
        $this->cuota_bs = null;
        $this->divisa_nombre = null;
    }

    public function cerrar_modificar_tarifa()
    {
        $this->modal_modificar_tarifa = false;
        $this->detalle_tarifa_id = null;
        $this->tarifa_nueva = 0;
    }


    public function updatedTarifa()
    {
        $this->tarifa_convertida = floatval(str_replace(',', '.', str_replace('.', '', $this->tarifa)));
        $this->actualizarPreviewBs();
    }

    public function updatedParametroId()
    {
        $this->actualizarPreviewBs();
    }

    private function actualizarPreviewBs()
    {
        if (!$this->parametro_id || !$this->tarifa_convertida) {
            $this->preview_bs = 0;
            return;
        }
        $divisa = Parametro::find($this->parametro_id);
        if (!$divisa || !$divisa->valor) {
            $this->preview_bs = 0;
            return;
        }
        $this->preview_bs = bcmul((string)$this->tarifa_convertida, (string)$divisa->valor, 2);
    }

    public function modificar_tarifa($detalle_id)
    {
        $detalle = ContratoCorporativoDetalle::where('contrato_corporativo_detalle_id', $detalle_id)->first();
        if (!$detalle) {
            $this->dispatch('alertSuccess2', message: 'Cuota no encontrada.');
            return;
        }
        $this->modal_modificar_tarifa = true;
        $this->detalle_tarifa_id = $detalle->contrato_corporativo_detalle_id;
        $this->tarifa_nueva = $detalle->cuota;
    }

    public function cambiar_tarifa()
    {
        $this->validate([
            'tarifa_nueva' => 'required|regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/',
        ]);

        $tarifa_n = floatval(str_replace(',', '.', str_replace('.', '', $this->tarifa_nueva)));

        $cuota_cambiar = ContratoCorporativoDetalle::where('contrato_corporativo_detalle_id', $this->detalle_tarifa_id)->first();
        if (!$cuota_cambiar) {
            $this->dispatch('alertSuccess2', message: 'Cuota no encontrada.');
            return;
        }
        $cuota_cambiar->cuota = $tarifa_n;
        $cuota_cambiar->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se modificó la tarifa de la cuota de almacenamiento ({$cuota_cambiar->contrato_corporativo_detalle_id}) a Bs {$tarifa_n}",
        ]);

        $this->dispatch('alertSuccess', message: 'Cuota modificada correctamente');

        $this->cerrar_modificar_tarifa();
    }

    #[Computed]
    public function detalle_actual()
    {
        return $this->detalle_id
            ? ContratoCorporativoDetalle::find($this->detalle_id)
            : null;
    }

    #[Computed]
    public function detalle_modificar()
    {
        return $this->detalle_tarifa_id
            ? ContratoCorporativoDetalle::find($this->detalle_tarifa_id)
            : null;
    }

    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }

    public function aprobar_pago()
    {
        $detalle = ContratoCorporativoDetalle::find($this->detalle_id);
        if (!$detalle) {
            $this->dispatch('alertSuccess2', message: 'Cuota no encontrada.');
            return;
        }

        if ($this->monto_pagado >= $this->cuota_bs) {
            $this->actualizar_contrato($detalle);
        } else {
            $this->dispatch(
                'alertSuccess2',
                message: 'El monto pagado debe ser igual o mayor a Bs ' . number_format($this->cuota_bs, 2, ',', '.')
            );
        }
    }

    private function actualizar_contrato($detalle)
    {
        $detalle->cancelada = true;
        $detalle->fecha_cancelada = Carbon::now();
        $detalle->tasa_pago = $this->tasa_aplicada;
        $detalle->monto_bs_pagado = $this->cuota_bs;
        $detalle->save();

        foreach ($this->pagos as $pago) {
            FacturacionContrato::create([
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'contrato_almacenamiento_id' => $this->contrato_id,
                'contrato_corporativo_detalle_id' => $detalle->contrato_corporativo_detalle_id,
                'tipo_pago_id' => $pago['tipo_pago_id'],
                'monto' => $pago['monto'],
                'referencia' => $pago['numero_referencia'] ?? null,
                'tasa_aplicada' => $this->tasa_aplicada,
                'monto_divisa' => $this->tasa_aplicada > 0
                    ? bcdiv((string)$pago['monto'], (string)$this->tasa_aplicada, 2)
                    : null,
            ]);
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se aprobó el pago de la cuota de almacenamiento ({$detalle->contrato_corporativo_detalle_id}) del contrato ({$this->contrato_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Cuota pagada correctamente!');
        $this->cerrar_pago();
    }

    public function crear_contrato()
    {
        if (is_string($this->metros_cuadrados)) {
            $this->metros_cuadrados = str_replace(',', '.', $this->metros_cuadrados);
        }

        $this->validate([
            'cliente_corporativo' => 'required|exists:clientes_corporativos,cliente_corporativo_id',
            'metros_cuadrados'    => 'required|numeric|min:0.01|max:99999.99',
            'parametro_id'        => 'required|exists:parametro,parametro_id',
            'tarifa'              => 'required|regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/',
            'tarifa_convertida'   => 'required|numeric|min:0.01',
            'cuotas'              => 'required|integer|min:1|max:60',
        ], [
            'tarifa.regex' => 'El formato de la tarifa no es válido.',
        ]);

        DB::beginTransaction();

        try {
            $fecha_inicio = Carbon::now();

            $contrato = ContratoAlmacenamiento::create([
                'oficina_id'             => $this->usuario['oficina_id'],
                'usuario_id'             => $this->usuario['id'],
                'cliente_corporativo_id' => $this->cliente_corporativo,
                'espacio'                => $this->metros_cuadrados,
                'fecha_inicio'           => $fecha_inicio,
                'fecha_fin'              => $fecha_inicio->copy()->addMonths($this->cuotas),
                'parametro_id'           => $this->parametro_id,
                'monto_divisa'           => $this->tarifa_convertida,
                'tarifa'                 => $this->tarifa_convertida,
                'activo'                 => true,
            ]);

            $tarifa_cuota = bcdiv((string)$this->tarifa_convertida, (string)$this->cuotas, 2);

            for ($i = 1; $i <= $this->cuotas; $i++) {
                ContratoCorporativoDetalle::create([
                    'cliente_corporativo_id'      => $this->cliente_corporativo,
                    'contrato_almacenamiento_id'  => $contrato->contrato_almacenamiento_id,
                    'cuota'                       => $tarifa_cuota,
                    'fecha_limite'                => $fecha_inicio->copy()->addMonths($i),
                    'cancelada'                   => false,
                    'fecha_cancelada'             => null,
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se creó el contrato de almacenamiento ({$contrato->contrato_almacenamiento_id}) con {$this->cuotas} cuota(s)",
            ]);

            DB::commit();
            $this->dispatch('alertSuccess', message: 'Contrato registrado exitosamente!');
            $this->dispatch('cliente_registrado');
            $this->modalClose();
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en la creacion del contrato!');
        }
    }

    public function render()
    {
        $clientes = ContratoAlmacenamiento::query()
            ->where('oficina_id', $this->usuario['oficina_id'])
            ->when($this->search, function ($query) {
                $query->whereHas('cliente', function ($q) {
                    $q->where('razon_social', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->orderBy('contrato_almacenamiento_id', 'asc')
            ->paginate($this->perPage);

        $detalles = $this->ver_detalles && $this->contrato_id
            ? ContratoCorporativoDetalle::with('almacenamiento.divisa')
                ->where('contrato_almacenamiento_id', $this->contrato_id)
                ->orderBy('cancelada', 'asc')
                ->orderBy('fecha_limite', 'asc')
                ->get()
            : collect();

        return view('livewire.almacenamiento.almacenamiento', compact('clientes', 'detalles'));
    }
}