<?php

namespace App\Livewire\TarjetasPostales;

use App\Models\Ciudad;

use App\Models\Estado;
use App\Models\Sector;
use App\Models\Cliente;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\TipoPago;
use App\Models\Municipio;
use App\Models\Parroquia;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Models\RegistroTarjetaPostal;
use App\Models\TarifaNacionalConcepto;
use App\Models\SeguimientoProducto;
use App\Models\Producto;
use App\Models\ProductoOficina;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class TarjetasPostales extends Component
{
    public $tipo_documento, $monto_servicio, $iva, $total, $documento, $nombre, $apellido, $telefono, $correo, $direccion, $monto,
        $monto_pagado, $metodo_pago_seleccionado, $cliente_existe, $mensaje_pago, $tarjetas_postales, $costo_tarjeta_postal;

    public $metodos_pago = [];
    public $pagos = [];
    public $pago_registrados = [];
    public $taquilla_postal = null;
    public $oficina = [];
    public $usuario = [];
    public $update = false;

    public $producto = 0;
    public $producto_id;
    public $cantidad;
    public $cantidad_a_agregar;
    public $productos_oficina;

    protected $rules = [
        'tipo_documento' => 'required',
        'documento' => 'required|digits_between:6,8',
        'nombre' => 'required|max:25|regex:/^[A-Za-z ]+$/',
        'apellido' => 'required|max:25|regex:/^[A-Za-z ]+$/',
        'telefono' => ['required', 'regex:/^(0424|0212|0414|0416|0426)\d{7}$/'],
        'correo' => 'required|email',
        'tarjetas_postales' => 'required|numeric|min:1|max:999',
    ];

    protected $messages = [
        'tipo_documento.required' => 'El tipo de documento es obligatorio',

        'documento.required' => 'El documento es obligatorio',
        'documento.digits_between' => 'El documento debe tener entre 6 y 8 digitos',

        'nombre.required' => 'El nombre es obligatorio',
        'nombre.max' => 'El maximo de caracteres es de 25',
        'nombre.regex' => 'Solo se permiten letras',

        'apellido.required' => 'El apellido es obligatorio',
        'apellido.max' => 'El maximo de caracteres es de 25',
        'apellido.regex' => 'Solo se permiten letras',

        'telefono.required' => 'El nro de telefono es obligatorio',
        'telefono.regex' => 'Debe iniciar con un codigo de area valido y al menos 11 digitos',

        'correo.required' =>  'El correo es obligatorio',
        'correo.email' => 'Debe ser en formato de correo electronico',

        'tarjetas_postales.required' => 'La cantidad de Tarjetas es obligatoria',
        'tarjetas_postales.numeric' => 'Debe ser un numero entero',
        'tarjetas_postales.min' => 'La cantidad minima de tarjetas postales es 1',
        'tarjetas_postales.max' => 'La cantidad maxima de tarjetas postales es 999',

        'metodo_pago_seleccionado.required' => 'Debe seleccionar un metodo de pago',
        'monto.required' => 'El monto es obligatorio',
        'monto.regex' => 'El monto debe ser de de tipo numerico',

        'pago_registrados.required' => 'Debe seleccionar un metodo de pago realizado'
    ];

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->metodos_pago = TipoPago::all();
        $this->costo_tarjeta_postal = TarifaNacionalConcepto::where('tarifa_conceptos_id', 3)->pluck('monto')->first();
        $this->producto = Producto::find(1);
    }



    public function Habilitar()
    {
        $this->validate();
        if ($this->monto_pagado > 0 && $this->monto_pagado >= $this->total) {
            $this->submit();
        } else {
            $this->mensaje_pago = 'El monto total pagado no corresponde al total a pagar por la venta';
        }
    }

    public function updatedTarjetasPostales()
    {
        $this->tarjetas_postales = preg_replace('/\D/', '', $this->tarjetas_postales);

        if ($this->tarjetas_postales > 999) {
            $this->tarjetas_postales = 999;
        }
        if ($this->tarjetas_postales < 1) {
            $this->monto_servicio = 0;
            $this->iva = 0;
            $this->total = 0;
        } else {
            $this->monto_servicio = (ceil(($this->tarjetas_postales * $this->costo_tarjeta_postal) * 100) / 100);
            $this->iva = (ceil(($this->monto_servicio * 0.16) * 100) / 100);
            $this->total = (ceil(($this->monto_servicio + $this->iva) * 100) / 100);
        }
    }


    public function updatedDocumento()
    {
        $this->documento = preg_replace('/[^\d]/', '', $this->documento);
        if (!empty($this->documento)) {
            $cliente = Cliente::where('numero_documento', $this->documento)->first();

            if ($cliente) {
                $this->nombre = $cliente->nombre;
                $this->apellido = $cliente->apellido;
                $this->tipo_documento = $cliente->tipo_documento;
                $this->telefono = $cliente->telefono;
                $this->correo = $cliente->correo;
                $this->cliente_existe = 1;
            } else {
                $this->cliente_existe = 0;
            }
        }
    }


    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }



    public function submit()
    {
        DB::beginTransaction();

        try {
            if ($this->cliente_existe == 0) {
                //Guardar cliente remitente
                Cliente::Create([
                    'numero_documento' => $this->documento,
                    'nombre' => $this->nombre,
                    'apellido' => $this->apellido,
                    'tipo_documento' => $this->tipo_documento,
                    'telefono' => $this->telefono,
                    'correo' => $this->correo,
                ]);
            }

            $nuevo_registro = RegistroTarjetaPostal::create([
                'servicio_id' => 6,
                'usuario_id' => $this->usuario['id'],
                'oficina_id' => $this->oficina['oficina_id'],
                'tipo_documento' => $this->tipo_documento,
                'documento' => $this->documento,
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'telefono' => $this->telefono,
                'correo' => $this->correo,
                'cantidad_tarjetas' => $this->tarjetas_postales,
            ]);

            // --- INICIO: REGISTRO DE FACTURACIÓN GENÉRICA MULTI-PAGO ---
            $facturacion = \App\Models\FacturacionServicio::create([
                'servicio_id' => 6, // Servicio de Tarjetas Postales
                'referencia_id' => $nuevo_registro->getKey(), // Obtiene la clave primaria real del registro
                'tipo_documento' => $this->tipo_documento,
                'documento' => $this->documento,
                'monto_subtotal' => $this->monto_servicio,
                'monto_iva' => $this->iva,
                'monto_total' => $this->total,
                'oficina_id' => $this->oficina['oficina_id'],
                'usuario_id' => $this->usuario['id'],
            ]);

            foreach ($this->pagos as $pago) {
                \App\Models\FacturacionServicioPago::create([
                    'facturacion_servicio_id' => $facturacion->facturacion_servicio_id,
                    'tipo_pago_id' => $pago['tipo_pago_id'] ?? $pago['tipo_pago'],
                    'monto' => $pago['monto'],
                    'referencia_bancaria' => $pago['referencia_bancaria'] ?? $pago['referencia'] ?? null,
                ]);
            }
            // --- FIN: REGISTRO DE FACTURACIÓN GENÉRICA MULTI-PAGO ---

            $this->restar_tarjetas();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró la compra de {$this->tarjetas_postales} tarjeta(s) postal(es) registro ({$nuevo_registro->getKey()})",
            ]);

            DB::commit(); // Si todo sale bien, confirmamos la transacción
            $this->dispatch('alertSuccess', message: 'Compra Aprobada Exitosamente');

            $this->dispatch('servicioAceptado');
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e);
            $this->dispatch('alertSuccess2', message: 'Compra Negada, Compruebe los datos e intente de nuevo');
            // Si ocurre un error, revertimos todos los cambios
        }
    }


    public function editar($id)
    {
        $this->update = true;
        $this->producto_id = $id;
        $producto = Producto::find($id);
        if ($producto) {
            $this->cantidad = $producto->cantidad; // Solo para mostrar la cantidad actual
        }
        $this->cantidad_a_agregar = ''; // Limpiar el input cada vez que se abre el modal
    }

    public function actualizar()
    {
        $this->validate([
            'cantidad_a_agregar' => 'required|numeric|min:1',
        ], [
            'cantidad_a_agregar.required' => 'La cantidad a agregar es obligatoria',
            'cantidad_a_agregar.numeric' => 'La cantidad debe ser un numero entero',
            'cantidad_a_agregar.min' => 'La cantidad minima a agregar es 1',
        ]);

        $productoOficina = ProductoOficina::where('oficina_id', $this->oficina['oficina_id'])
            ->where('producto_id', $this->producto_id)
            ->first();

        if ($productoOficina) {
            $productoOficina->cantidad += $this->cantidad_a_agregar;
            $productoOficina->save();

            SeguimientoProducto::create([
                'user_id' => $this->usuario['id'],
                'oficina_id' => $this->oficina['oficina_id'],
                'producto_id' => $this->producto['producto_id'],
                'operacion' => true,
                'cantidad_registrada' => $this->cantidad_a_agregar,
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se actualizó la cantidad del producto ({$this->producto_id}) sumando {$this->cantidad_a_agregar} unidad(es) en la oficina ({$this->oficina['oficina_id']})",
            ]);

            $this->dispatch('alertSuccess', message: 'Cantidad actualizada correctamente');
        } else {
            ProductoOficina::create([
                'oficina_id' => $this->oficina['oficina_id'],
                'producto_id' => $this->producto_id,
                'cantidad' => $this->cantidad_a_agregar,
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se agregó el producto ({$this->producto_id}) a la oficina ({$this->oficina['oficina_id']}) con {$this->cantidad_a_agregar} unidad(es)",
            ]);

            $this->dispatch('alertSuccess', message: 'Producto agregado a la oficina correctamente');
        }
    }
    public function restar_tarjetas()
    {
        $productos_oficina_totales = ProductoOficina::where('oficina_id', $this->oficina['oficina_id'])
            ->where("producto_id", 1)->first();
        if ($productos_oficina_totales->cantidad >= $this->tarjetas_postales) {
            $productos_oficina_totales->cantidad -= $this->tarjetas_postales;
            $productos_oficina_totales->save();
            SeguimientoProducto::create([
                'user_id' => $this->usuario['id'],
                'oficina_id' => $this->oficina['oficina_id'],
                'producto_id' => $this->producto['producto_id'],
                'operacion' => false,
                'cantidad_registrada' => $this->tarjetas_postales,
            ]);
        }
    }

    public function cerrar_editar()
    {
        $this->update = false;
        $this->producto_id = '';
        $this->cantidad = '';
    }

    public function render()
    {
        $this->productos_oficina = ProductoOficina::where('oficina_id', $this->oficina['oficina_id'])
            ->where("producto_id", 1)->first();
        return view('livewire.tarjetas-postales.tarjetas-postales');
    }
}
