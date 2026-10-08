<?php

namespace App\Livewire\AlianzaRecaudacion;

use App\Models\Alianza;
use Livewire\Component;
use App\Models\TipoAlianza;
use Livewire\WithPagination;
use App\Models\UsuarioSeguimiento;

use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Models\ClienteCorporativo;

#[Layout('layouts.app')]
class AlianzaRecaudacion extends Component
{
    use WithPagination;

    public $cliente, $tipo_alianza, $porcentaje, $porcentaje_real;

    public $search = '';
    public $perPage = 5;
    public $showModal = false;
    public $alianza_id = null;
    public $tipos_alianzas = [];

    // --- Registro de pagos (solo vista: aún no se persiste) ---
    public $showModalPago = false;
    public $pago_cliente;
    public $pago_alianza;
    public $observacion_pago;
    public $monto_pagado = 0;
    public $pagos = [];

    public function mount()
    {
        $this->tipos_alianzas = TipoAlianza::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedPorcentaje()
    {
        // El input llega formateado a la venezolana ("1.234,56"): el punto es
        // separador de miles y la coma el decimal.
        $this->porcentaje_real = floatval(str_replace(',', '.', str_replace('.', '', $this->porcentaje)));
    }

    public function alianza($id)
    {
        $this->resetValidation();

        $this->cliente = ClienteCorporativo::where('cliente_corporativo_id', $id)->first();
        $comprobar = Alianza::where('cliente_corporativo_id', $id)->first();

        if($comprobar){
            $this->alianza_id = $comprobar->getKey();
            $this->tipo_alianza = $comprobar->tipo_alianza_id;
            $this->porcentaje = number_format((float) $comprobar->porcentaje, 2, ',', '.');
            $this->updatedPorcentaje();
        }else{
            $this->alianza_id = null;
            $this->tipo_alianza = '';
            $this->porcentaje = '';
            $this->porcentaje_real = 0;
        }

        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['cliente', 'alianza_id', 'tipo_alianza', 'porcentaje', 'porcentaje_real']);
    }

    // ===========================================
    // REGISTRO DE PAGOS
    // ===========================================

    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }

    public function registrarPago($id)
    {
        $this->resetValidation();

        $this->pago_cliente = ClienteCorporativo::where('cliente_corporativo_id', $id)->first();
        $this->pago_alianza = Alianza::with('alianza')
            ->where('cliente_corporativo_id', $id)
            ->first();

        // Sin alianza no hay porcentaje que aplicar.
        if (! $this->pago_alianza) {
            $this->dispatch('alertSuccess3', message: 'El cliente no tiene una alianza registrada. Registre la alianza antes de cargar un pago.');
            return;
        }

        $this->observacion_pago = '';
        $this->monto_pagado = 0;
        $this->pagos = [];

        $this->showModalPago = true;
    }

    public function cerrarModalPago()
    {
        $this->showModalPago = false;
        $this->resetValidation();
        $this->reset(['pago_cliente', 'pago_alianza', 'observacion_pago', 'monto_pagado', 'pagos']);
    }

    /** Porcentaje vigente de la alianza seleccionada. */
    public function getPorcentajeAlianzaProperty()
    {
        return (float) ($this->pago_alianza->porcentaje ?? 0);
    }

    /** Parte que le corresponde a IPOSTEL sobre lo cobrado. */
    public function getMontoComisionProperty()
    {
        return round(((float) $this->monto_pagado) * ($this->porcentajeAlianza / 100), 2);
    }

    /** Remanente que le queda al aliado. */
    public function getMontoAliadoProperty()
    {
        return round(((float) $this->monto_pagado) - $this->montoComision, 2);
    }

    public function guardar_pago()
    {
        if (! $this->pago_alianza) {
            $this->dispatch('alertSuccess2', message: 'No se pudo determinar la alianza del cliente.');
            return;
        }

        if (count($this->pagos) === 0 || (float) $this->monto_pagado <= 0) {
            $this->dispatch('alertSuccess2', message: 'Debe registrar al menos un método de pago antes de continuar.');
            return;
        }

        // TODO: pendiente definir con el equipo dónde se persiste la recaudación
        // (FacturacionServicio + tabla de reparto). Por ahora solo se muestra el
        // cálculo en pantalla y no se guarda nada en base de datos.
        $this->dispatch('alertSuccess3', message: 'Cálculo realizado. El registro en base de datos aún no está habilitado.');
    }

    public function registrar_alianza()
    {
        $this->validate([
            'porcentaje' => [
                'required',
                'regex:/^(100([,.]0{1,2})?|(\d{1,2})([,.]\d{1,2})?)$/'
            ],
            'tipo_alianza' => 'required',
        ], [
            'porcentaje.required' => 'Debe ingresar un porcentaje.',
            'porcentaje.regex'    => 'El porcentaje debe ser un número entre 1 y 100 (máx. 2 decimales).',
            'tipo_alianza.required' => 'Debe seleccionar un tipo de alianza.',
        ]);

        if (! $this->cliente) {
            $this->dispatch('alertSuccess2', message: 'No se pudo determinar el cliente corporativo.');
            return;
        }

        // El regex valida el formato del texto; esto asegura que el valor ya
        // convertido a float tampoco se salga del rango.
        if ($this->porcentaje_real <= 0 || $this->porcentaje_real > 100) {
            $this->addError('porcentaje', 'El porcentaje debe ser un número entre 1 y 100 (máx. 2 decimales).');
            return;
        }

        DB::beginTransaction();

        try {
            $edicion = (bool) $this->alianza_id;

            $alianza = Alianza::updateOrCreate(
                ['cliente_corporativo_id' => $this->cliente['cliente_corporativo_id']],
                [
                    'tipo_alianza_id' => $this->tipo_alianza,
                    'porcentaje' => $this->porcentaje_real,
                    'activo' => true,
                ]
            );

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => $edicion ? 'update' : 'create',
                'descripcion' => $edicion
                    ? "Se actualizó la alianza de recaudación ({$alianza->getKey()}) del cliente corporativo ({$this->cliente['cliente_corporativo_id']})"
                    : "Se registró alianza de recaudación ({$alianza->getKey()}) con el cliente corporativo ({$this->cliente['cliente_corporativo_id']})",
            ]);

            DB::commit();

            $this->dispatch('alertSuccess', message: $edicion
                ? 'Alianza actualizada correctamente'
                : 'Alianza registrada correctamente');

            $this->cerrarModal();
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrió un error al guardar la alianza, verifique los datos e intente de nuevo');
        }
    }

    public function render()
    {
        $autorizados = ClienteCorporativo::with('alianza.alianza')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('razon_social', 'ilike', '%' . $this->search . '%')
                        ->orWhere('numero_documento', 'ilike', '%' . $this->search . '%')
                        ->orWhere('correo', 'ilike', '%' . $this->search . '%')
                        ->orWhere('telefono', 'ilike', '%' . $this->search . '%');
                });
            })
            ->orderBy('razon_social')
            ->paginate($this->perPage);

        return view('livewire.alianza-recaudacion.alianza-recaudacion', ['autorizados' => $autorizados]);
    }
}
