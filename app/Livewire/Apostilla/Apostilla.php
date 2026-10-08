<?php

namespace App\Livewire\Apostilla;

use App\Models\Apostillas;
use App\Models\CitaApostilla;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\DocumentoApostilla;
use App\Models\FacturacionServicio;
use App\Models\FacturacionServicioPago;
use App\Models\Oficina;
use App\Models\TipoPago;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Apostilla extends Component
{
    public $usuario, $oficina, $metodos_pago, $monto, $precio_total, $pago_total, $tipo_documento, $documento, $nombre,
        $apellido, $telefono, $correo;

    public $monto_pagado = 0;
    public $tipo_registro = 1;
    public $tipos_documentos = [];
    public $metodo_pago_seleccionado = [];
    public $pagos = [];
    public $documentos = [];
    public $doc_selec = [];


    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->tipos_documentos = Documento::all();
        $this->documentos = DocumentoApostilla::all();
        $this->metodos_pago = TipoPago::all();
    }

    protected $rules = [
        'precio_total' => 'required',
        'nombre' => 'required|max:20',
        'apellido' => 'required|max:20',
        'tipo_documento' => 'required',
        'documento' => 'required|digits_between:8,12'
    ];

    protected $messages = [
        'tipo_documento.required' => 'El campo es obligatorio',
        'documento.required' => 'El campo es obligatorio',
        'nombre.required' => 'El campo es obligatorio',
        'apellido.required' => 'El campo es obligatorio',
        'telefono.required' => 'El campo es obligatorio',
        'correo.required' => 'El campo es obligatorio',
        'doc_selec.required' => 'Debe seleccionar al menos un documento'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }


    public function updatedDocumento()
    {
        if ($this->documento == '') {
            return;
        } else {

            $cliente = Cliente::where('numero_documento', $this->documento)->first();

            if ($cliente) {
                $this->nombre = $cliente->nombre;
                $this->apellido = $cliente->apellido;
                $this->tipo_documento = $cliente->tipo_documento;
                $this->telefono = $cliente->telefono;
                $this->correo = $cliente->correo;
            } else {
                return;
            }
        }
    }

    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }

    public function updatedPrecioTotal()
    {
        $this->pago_total = floatval(str_replace(',', '.', str_replace('.', '', $this->precio_total)));
    }

    public function habilitar()
    {
        if ($this->monto_pagado > 0 && $this->monto_pagado >= $this->pago_total) {
            $this->cargar_apostilla();
        } else {
            $this->dispatch('alertSuccess2', message: 'Error, el monto pagado no coincide con el monto a pagar');
        }
    }

    public function cargar_apostilla()
    {
        $this->validate([
            'doc_selec' => 'required|array|min:1',
        ], [
            'doc_selec.required' => 'Debes seleccionar al menos un documento.',
            'doc_selec.min' => 'Selecciona al menos un documento.',
        ]);

        $this->validate();

        DB::beginTransaction();
        try {

            $cita = CitaApostilla::create([
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'tipo_documento' => $this->tipo_documento,
                'documento' => $this->documento,
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'correo' => $this->correo,
                'telefono' => $this->telefono,
                'estatus' => $this->tipo_registro,
                'coste' => $this->monto_pagado,
            ]);

            foreach ($this->doc_selec as $doc) {
                Apostillas::create([
                    'documento_apostilla_id' => $doc,
                    'cita_apostilla_id' => $cita->cita_apostilla_id,
                ]);
            }

            // --- INICIO: REGISTRO DE FACTURACIÓN GENÉRICA MULTI-PAGO ---
            $facturacion = FacturacionServicio::create([
                'servicio_id' => 26,
                'referencia_id' => $cita->cita_apostilla_id,
                'tipo_documento' => $this->tipo_documento,
                'documento' => $this->documento,
                'monto_subtotal' => $this->pago_total,
                'monto_iva' => 0,
                'monto_total' => $this->pago_total,
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
            ]);

            foreach ($this->pagos as $pago) {
                FacturacionServicioPago::create([
                    'facturacion_servicio_id' => $facturacion->facturacion_servicio_id,
                    'tipo_pago_id' => $pago['tipo_pago_id'] ?? $pago['tipo_pago'],
                    'monto' => $pago['monto'],
                    'referencia_bancaria' => $pago['referencia_bancaria'] ?? $pago['referencia'] ?? null,
                ]);
            }
            // --- FIN: REGISTRO DE FACTURACIÓN GENÉRICA MULTI-PAGO ---

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró la cita de apostilla ({$cita->cita_apostilla_id}) con " . count($this->doc_selec) . " documento(s)",
            ]);

            DB::commit(); // Si todo sale bien, confirmamos la transacción
            $this->dispatch('alertSuccess', message: 'Registro de Apostilla Creado exitosamente!');
            $this->dispatch('recargarPagina');
        } catch (\Exception $e) {
            //    dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en el registro, verifique los datos e intente de nuevo');
            return;
            // Si ocurre un error, revertimos todos los cambios
        }
    }

    public function render()
    {
        return view('livewire.apostilla.apostilla');
    }
}
