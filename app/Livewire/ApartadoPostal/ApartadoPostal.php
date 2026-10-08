<?php

namespace App\Livewire\ApartadoPostal;

use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\ClienteCorporativo;
use App\Models\CodigoApartadoPostal;
use App\Models\Documento;
use App\Models\Estado;
use App\Models\FacturacionServicio;
use App\Models\FacturacionServicioPago;
use App\Models\Municipio;
use App\Models\Oficina;
use App\Models\Parroquia;
use App\Models\RegistroApartado;
use App\Models\Sector;
use App\Models\TarifaNacionalConcepto;
use App\Models\TipoPago;
use App\Models\UsuarioSeguimiento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ApartadoPostal extends Component
{

    public $tipo_documento, $monto_servicio, $iva, $total, $documento, $nombre, $apellido, $estado, $municipio, $parroquia, $ciudad,
        $codigo_postal, $telefono, $correo, $direccion, $monto, $monto_pagado, $metodo_pago_seleccionado, $cliente_existe,
        $apartado_postal;

    public $apartados_postales;
    public $documentos = [];
    public $usuario = [];
    public $oficina = [];
    public $metodos_pago = [];
    public $pagos = [];
    public $pago_registrados = [];
    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $ciudades = [];
    public $cantidad_tarjetas_postales = null;


    protected $rules = [
        'tipo_documento' => 'required',
        'documento' => 'required|digits_between:6,8',
        'nombre' => 'required|max:25|regex:/^[A-Za-z ]+$/',
        'apellido' => 'required|max:25|regex:/^[A-Za-z ]+$/',
        'telefono' => ['required', 'min:11', 'max:11'],
        'correo' => 'required|email',
        'apartado_postal' => 'required',
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

        'apartado_postal.required' => 'El nro de Taquilla es obligatorio',
        'apartado_postal.digits' => 'Debe contener al menos 4 digitos',

        'metodo_pago_seleccionado.required' => 'Debe seleccionar un metodo de pago',
        'monto.required' => 'El monto es obligatorio',
        'monto.regex' => 'El monto debe ser de de tipo numerico',

        'pago_registrados.required' => 'Debe seleccionar un metodo de pago realizado'
    ];

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->documentos = Documento::all();
        $this->ciudades = Ciudad::where('municipio_id', $this->oficina['municipio_id'])->get();
        $this->metodos_pago = TipoPago::all();
        $this->apartados_postales = CodigoApartadoPostal::where('operativo', true)->where('activo', false)
            ->where('oficina_id', $this->usuario['oficina_id'])->orderBy('codigo_apartado_id', 'asc')->get();
    }


    public function Habilitar()
    {
        $this->validate();
        if ($this->monto_pagado >= 0 && $this->monto_pagado >= $this->total) {
            $this->submit();
        } else {
            $this->dispatch('alertSuccess2', message: 'El monto pagado no corresponde con el total a pagar');
        }
    }


    public function updatedTipoDocumento()
    {
        if ($this->tipo_documento == '') {
            $this->monto_servicio = '0';
        } else {
            if ($this->tipo_documento === 'V' || $this->tipo_documento === 'E') {
                $this->monto_servicio = TarifaNacionalConcepto::where('tarifa_conceptos_id', 1)->pluck('monto')->first();
            } else {
                $this->monto_servicio = TarifaNacionalConcepto::where('tarifa_conceptos_id', 2)->pluck('monto')->first();
            }
        }

        $this->iva = bcdiv((string)($this->monto_servicio * 0.16), 1, 2);
        $this->total = bcdiv((string)($this->monto_servicio + $this->iva), 1, 2);
    }



    public function updatedDocumento()
    {
        // Limpiar el número de documento para que solo contenga dígitos
        $this->documento = preg_replace('/[^\d]/', '', $this->documento);

        if (!empty($this->documento)) {
            // Intentar obtener el cliente de la tabla Cliente
            $cliente = Cliente::where('numero_documento', $this->documento)->first();

            // Si no se encuentra en la tabla Cliente, buscar en la tabla ClienteCorporativo
            if (!$cliente) {
                $cliente = ClienteCorporativo::where('numero_documento', $this->documento)->first();
            }

            // Si se encuentra el cliente en cualquiera de las dos tablas, asignar los valores
            if ($cliente) {
                $this->setClienteData($cliente);
                $this->cliente_existe = 1;
                $this->updatedTipoDocumento();
            } else {
                $this->cliente_existe = 0;
            }
        }
    }

    private function setClienteData($cliente)
    {
        if ($cliente instanceof Cliente) {
            // Asignar datos de cliente de la tabla Cliente
            $this->nombre = $cliente->nombre;
            $this->apellido = $cliente->apellido;
            $this->tipo_documento = $cliente->tipo_documento;
            $this->telefono = $cliente->telefono;
            $this->correo = $cliente->correo;
        } elseif ($cliente instanceof ClienteCorporativo) {
            // Asignar datos de cliente de la tabla ClienteCorporativo
            $this->nombre = $cliente->razon_social;
            $this->apellido = $cliente->agente_autorizado;
            $this->tipo_documento = $cliente->tipo_documento;
            $this->telefono = $cliente->telefono;
            $this->correo = $cliente->correo;
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

            $nuevo_apartado = RegistroApartado::create([
                'servicio_id' => 2,
                'oficina_id' => $this->oficina['oficina_id'],
                'codigo_apartado_id' => $this->apartado_postal,
                'tipo_documento' => $this->tipo_documento,
                'documento' => $this->documento,
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'codigo_postal' => $this->oficina['codigo_ubicacion'],
                'telefono' => $this->telefono,
                'correo' => $this->correo,
                'direccion' => $this->oficina['direccion'],
                'activo' => true,
                'usuario_id' => $this->usuario['id'],
                'coste' => $this->monto_pagado,
            ]);

            // --- INICIO: REGISTRO DE FACTURACIÓN GENÉRICA MULTI-PAGO ---
            $facturacion = FacturacionServicio::create([
                'servicio_id' => 2, // Servicio de Apartado Postal
                'referencia_id' => $nuevo_apartado->registro_apartado_id,
                'tipo_documento' => $this->tipo_documento,
                'documento' => $this->documento,
                'monto_subtotal' => $this->monto_servicio,
                'monto_iva' => $this->iva,
                'monto_total' => $this->total,
                'oficina_id' => $this->oficina['oficina_id'],
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

            $cambio_estatus = CodigoApartadoPostal::find($this->apartado_postal);
            $cambio_estatus->activo = true;
            $cambio_estatus->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró el apartado postal ({$nuevo_apartado->registro_apartado_id}) para el código de apartado ({$this->apartado_postal})",
            ]);

            DB::commit(); // Si todo sale bien, confirmamos la transacción

        } catch (\Exception $e) {
            // dd($e);
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error, verifique los datos e intente de nuevo');
            DB::rollback();
            return; // Si ocurre un error, revertimos todos los cambios
        }

        $this->dispatch('alertSuccess', message: 'Servicio Aprobado Exitosamente');
        $this->dispatch('servicioAceptado');
    }


    public function render()
    {
        return view('livewire.apartado-postal.apartado-postal');
    }
}
