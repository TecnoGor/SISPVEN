<?php

namespace App\Livewire\Imprenta;

use App\Models\Oficina;
use Livewire\Component;
use App\Models\Documento;
use Livewire\WithFileUploads;
use App\Models\PedidoImprenta;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class Imprenta extends Component
{
    use WithFileUploads;

    public $tipos_documentos, $pago_total, $tipo_documento, $documento, $nombre, $apellido, $telefono, $correo, $imagen, $alto,
        $ancho, $fecha_entrega;

    public $oficina;
    public $monto_pagado = 0;
    public $usuario = [];
    public $pagos = [];

    protected $rules = [
        'imagen'         => 'required|image|mimes:jpg,jpeg,png|max:51200',
        'alto'           => 'required|numeric|min:0.1',
        'ancho'          => 'required|numeric|min:0.1',
        'fecha_entrega'  => 'required|date|after_or_equal:today',
        'tipo_documento' => 'required|string',
        'documento'      => 'required|string|min:6|max:20',
        'nombre'         => 'required|string|max:255',
        'apellido'       => 'required|string|max:255',
        'telefono'       => 'required|string|max:20',
        'correo'         => 'required|email|max:255',
        'pago_total'     => 'required',
    ];

    protected $messages = [
        'imagen.required'         => 'La imagen es obligatoria.',
        'imagen.image'            => 'El archivo debe ser una imagen.',
        'imagen.mimes'            => 'La imagen debe ser JPG o PNG.',
        'imagen.max'              => 'La imagen no debe superar los 50MB.',
        'alto.required'           => 'El alto es obligatorio.',
        'alto.numeric'            => 'El alto debe ser un valor numérico.',
        'alto.min'                => 'El alto debe ser mayor a 0.',
        'ancho.required'          => 'El ancho es obligatorio.',
        'ancho.numeric'           => 'El ancho debe ser un valor numérico.',
        'ancho.min'               => 'El ancho debe ser mayor a 0.',
        'fecha_entrega.required'  => 'La fecha requerida es obligatoria.',
        'fecha_entrega.date'      => 'La fecha no es válida.',
        'fecha_entrega.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
        'tipo_documento.required' => 'El tipo de documento es obligatorio.',
        'documento.required'      => 'El número de documento es obligatorio.',
        'documento.min'           => 'El documento debe tener al menos 6 caracteres.',
        'documento.max'           => 'El documento no debe superar los 20 caracteres.',
        'nombre.required'         => 'El nombre es obligatorio.',
        'apellido.required'       => 'El apellido es obligatorio.',
        'telefono.required'       => 'El teléfono es obligatorio.',
        'correo.required'         => 'El correo es obligatorio.',
        'correo.email'            => 'El correo no es válido.',
        'pago_total.required'     => 'El total a pagar es obligatorio.',
    ];

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->tipos_documentos = Documento::all();
    }

    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }


    public function verificar_pago()
    {
        $this->validate();

        $pago_total = (float) str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $this->pago_total));

        if ($pago_total > 0) {

            if ($this->monto_pagado >= $pago_total) {
                $this->crear_pedido();
            } else {
                $this->dispatch('alertSuccess2', message: 'El monto pagado debe ser mayor o igual al total a pagar');
            }
        } else {
            $this->dispatch('alertSuccess2', message: 'El total a pagar debe ser mayor a cero');
        }
    }


    public function crear_pedido()
    {
        DB::beginTransaction();

        try {
            $pago_total = (float) str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $this->pago_total));

            $path = $this->imagen->store('imagenes_pedidos', 'public');

            $pedido = PedidoImprenta::create([
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'tipo_documento' => $this->tipo_documento,
                'documento' => $this->documento,
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'telefono' => $this->telefono,
                'correo' => $this->correo,
                'alto' => $this->alto,
                'ancho' => $this->ancho,
                'fecha_entrega' => $this->fecha_entrega,
                'imagen_path' => $path,
                'nombre_original' => $this->imagen->getClientOriginalName(),
                'activo' => true,
            ]);

            // Registro de facturación del servicio
            $facturacion = \App\Models\FacturacionServicio::create([
                'servicio_id' => 12,
                'referencia_id' => $pedido->getKey(),
                'tipo_documento' => $this->tipo_documento,
                'documento' => $this->documento,
                'monto_subtotal' => $pago_total,
                'monto_iva' => 0,
                'monto_total' => $pago_total,
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
            ]);

            foreach ($this->pagos as $pago) {
                \App\Models\FacturacionServicioPago::create([
                    'facturacion_servicio_id' => $facturacion->facturacion_servicio_id,
                    'tipo_pago_id' => $pago['tipo_pago_id'] ?? $pago['tipo_pago'],
                    'monto' => $pago['monto'],
                    'referencia_bancaria' => $pago['numero_referencia'] ?? null,
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró el pedido de imprenta ({$pedido->getKey()})",
            ]);

            DB::commit();
            $this->dispatch('alertSuccess', message: 'Pedido Registrado!');
            $this->dispatch('recargarPagina');
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en la creacion del pedido, intente de nuevo');
        }
    }

    public function render()
    {
        return view('livewire.imprenta.imprenta');
    }
}
