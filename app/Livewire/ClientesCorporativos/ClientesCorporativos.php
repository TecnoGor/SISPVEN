<?php

namespace App\Livewire\ClientesCorporativos;

use Carbon\Carbon;

use App\Models\Estado;
use App\Models\Sector;
use Livewire\Component;
use App\Models\TipoPago;
use App\Models\Documento;
use App\Models\Municipio;
use App\Models\Parametro;
use App\Models\Parroquia;
use App\Models\TipoContrato;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\ClienteCorporativo;
use Illuminate\Support\Facades\DB;
use App\Models\ContratoCorporativo;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Models\ContratoCorporativoDetalle;
use App\Exports\ClientesCorporativosExport;
use App\Models\ClienteCorporativoDirecciones;
use App\Models\Ciudad;
use App\Models\UsuarioSeguimiento;
use App\Rules\CodigosTelefono;

#[Layout('layouts.app')]
class ClientesCorporativos extends Component
{
    use WithPagination;
    public $estado, $municipio, $parroquia, $tipo_documento, $razon_social, $agente_autorizado, $latitud, $longitud,
        $telefono, $correo, $direccion, $codigo_postal, $pago_registrados, $total_pagar, $cant_envios,
        $precio_total, $iva, $tarifa, $cliente_corporativo, $peso, $peso_conver, $duracion, $fecha_inicio, $fecha_fin, $cliente_razon,
        $ultimo_contrato, $cliente_actualizacion, $desde, $hasta, $tipo_divisa, $monto_divisa;

    // Declarado aparte con type hint para que Livewire preserve los ceros a la izquierda
    public string $documento = '';

    public $cancelar_cuota = false;
    public $perPage = 10;
    public $search;
    public $crear_contrato = false;
    public $mostrar_contrato = false;
    public $usuario = [];
    public $modal_open = false;
    public $actualizar_cliente = false;
    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $codigos_postales = [];
    public $pagos = [];
    public $documentos = [];
    public $cliente_act = [];
    public $modal_estatus = false;
    public $tipos_contratos = [];
    public $contrato = [];
    public $tipo_contrato = [];
    public $divisas_activas = [];
    public $parametro_id;       // ID de la divisa elegida
    public $preview_bs = 0;     // previsualización del equivalente en Bs

    // — Direcciones —
    public $modal_direcciones = false;
    public $dir_cliente_id;
    public $dir_cliente_razon;
    public $dir_alias;
    public $dir_persona;
    public $dir_estado;
    public $dir_municipio;
    public $dir_ciudad;
    public $dir_parroquia;
    public $dir_codigo_postal;
    public $dir_direccion;
    public $dir_telefono;
    public $dir_correo;
    public $dir_municipios = [];
    public $dir_ciudades = [];
    public $dir_parroquias = [];
    public $dir_codigos_postales = [];
    public $modal_crear_direccion = false;


    public function rules()
    {
        return [
            'tipo_documento' => 'required_if:mostrar_contrato, false',
            'documento' => 'required_if:mostrar_contrato, false|digits_between:9,12',
            'razon_social' => 'required_if:mostrar_contrato, false|max:60',
            'agente_autorizado' => 'max:25|regex:/^[A-Za-z ]+$/',
            'telefono' => ['required_if:mostrar_contrato, false', new CodigosTelefono],
            'correo' => 'required_if:mostrar_contrato, false|email',
            'estado' => 'required_if:mostrar_contrato, false',
            'municipio' => 'required_if:mostrar_contrato, false',
            'parroquia' => 'required_if:mostrar_contrato, false',
            'codigo_postal' => 'required_if:mostrar_contrato, false',
            'direccion' => 'required_if:mostrar_contrato, false',
            'latitud' => 'numeric|between:0,90',
            'longitud' => 'numeric|between:-180,180',
            'cliente_actualizacion' => 'required_if:actualizar_cliente, true',
        ];
    }


    protected $messages = [
        'tipo_documento.required_if' => 'El campo es requerido',

        'documento.digits' => 'El campo debe tener entre 9 y 12 digitos',

        'razon_social.max' => 'Maximo de 60 caracteres',

        'agente_autorizado.max' => 'Maximo de 25 caracteres',
        'agente_autorizado.regex' => 'Solo caracteres Alfabeticos',

        'telefono.regex' => 'Debe contener un codigo de area valido seguido de 7 digitos',

        'correo.email' => 'Debe tener un formato valido',

        'latitud.required_if' => 'La latitud es requerido',
        'latitud.numeric' => 'La latitud debe ser un valor numérico.',
        'latitud.between' => 'La latitud debe estar entre -90 y 90.',

        'longitud.required_if' => 'La longitud es requerido',
        'longitud.numeric' => 'La longitud debe ser un valor numérico.',
        'longitud.between' => 'La longitud debe estar entre -180 y 180.',

        'cliente_actualizacion.required_if' => 'El campo es requerido',
    ];




    public function mount()
    {
        $this->usuario = auth()->user();
        $this->estados = Estado::where('pais_id', 90)->get();
        $this->documentos = Documento::whereNotIn('tipo', ['E'])->get();
        $this->cliente_act = ClienteCorporativo::all();
        $this->divisas_activas = Parametro::where('activo', true)->get();
    }


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function validarContrato()
    {
        if ($this->peso && $this->cant_envios) {
            $this->tipo_contrato = TipoContrato::where('tipo_contrato_id', 3)->first();
        } elseif ($this->peso && !$this->cant_envios) {
            $this->tipo_contrato = TipoContrato::where('tipo_contrato_id', 1)->first();
        } else {
            $this->tipo_contrato = TipoContrato::where('tipo_contrato_id', 2)->first();
        }
    }

    public function updatedEstado()
    {
        $this->municipio = '';

        if ($this->estado == '') {
            $this->municipios = [];
        } else {
            $this->municipios = Municipio::where('estado_id', $this->estado)->get();
        }
    }

    public function updatedMunicipio()
    {
        $this->parroquia = '';

        if ($this->municipio == '') {
            $this->parroquias = [];
        } else {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio)->get();
        }
    }

    public function updatedParroquia()
    {
        $this->codigo_postal = '';

        if ($this->parroquia == '') {
            $this->codigos_postales = [];
        } else {
            $this->codigos_postales = Sector::where('parroquia_id', $this->parroquia)->distinct()->pluck('codigo_postal')->sort();
        }
    }

    public function updatedParametroId()
    {
        $this->actualizarPrecioTotal();
    }

    public function updatedTarifa()
    {
        $this->actualizarPrecioTotal();
    }

    private function actualizarPrecioTotal()
    {
        $monto = floatval(str_replace(',', '.', str_replace('.', '', $this->tarifa)));
        $this->precio_total = $monto;
        $this->monto_divisa = $monto;

        // Calcular previsualización en Bs según la divisa elegida
        if ($this->parametro_id) {
            $tasa = Parametro::where('parametro_id', $this->parametro_id)->value('valor');
            $this->preview_bs = $tasa ? bcmul((string)$monto, (string)$tasa, 2) : 0;
        } else {
            $this->preview_bs = 0;
        }
    }


    public function updatedPeso()
    {
        if ($this->peso <= 0 || $this->peso == 0) {
            $this->peso = '';
            $this->peso_conver = 0;
        } else {
            $this->peso_conver = $this->peso * 1000;
        }
        $this->validarContrato();
    }

    public function updatedCantEnvios()
    {
        if ($this->cant_envios === '' || $this->cant_envios === null) {
            $this->cant_envios = null;
        } elseif ((int) $this->cant_envios < 0) {
            $this->cant_envios = null;
        } else {
            $this->cant_envios = (int) $this->cant_envios;
        }
        $this->validarContrato();
    }


    public function modalOpen()
    {
        $this->modal_open = true;
    }

    public function modalOpenContrato()
    {
        $this->crear_contrato = true;
    }

    public function modalOpenActualizar()
    {
        $this->actualizar_cliente = true;
    }

    public function updatedClienteActualizacion($value)
    {
        if ($value == '') {
            return;
        } else {

            $cliente = ClienteCorporativo::where('cliente_corporativo_id', $value)->first();

            $this->tipo_documento = $cliente->tipo_documento;
            $this->documento = $cliente->numero_documento;
            $this->razon_social = $cliente->razon_social;
            $this->agente_autorizado = $cliente->agente_autorizado;
            $this->latitud = $cliente->latitud;
            $this->longitud = $cliente->longitud;
            $this->telefono = $cliente->telefono;
            $this->correo = $cliente->correo;
            $this->direccion = $cliente->direccion;
        }
    }

    public function modalClose()
    {
        $this->modal_open = false;
        $this->actualizar_cliente = false;

        $this->tipo_documento = '';
        $this->documento = '';
        $this->razon_social = '';
        $this->agente_autorizado = '';
        $this->estado = '';
        $this->municipio = '';
        $this->parroquia = '';
        $this->codigo_postal = '';
        $this->latitud = '';
        $this->longitud = '';
        $this->telefono = '';
        $this->correo = '';
        $this->direccion = '';
    }

    public function cambiar_estatus($contrato_id)
    {
        $this->contrato = ContratoCorporativo::where('contrato_corporativo_id', $contrato_id)->pluck('contrato_corporativo_id');
        $this->modal_estatus = true;
    }

    public function verContrato($cliente_id)
    {
        $this->cliente_razon = ClienteCorporativo::where('cliente_corporativo_id', $cliente_id)
            ->pluck('razon_social')->first();

        $this->ultimo_contrato = ContratoCorporativo::where('cliente_corporativo_id', $cliente_id)->where('activo', true)->first();

        if ($this->ultimo_contrato) {
            $this->mostrar_contrato = true;
        } else {
            $this->dispatch('alertSuccess3', message: 'El cliente no posee un contrato activo');
        }
    }

    public function cerrar()
    {
        $this->crear_contrato = false;
        $this->peso = '';
        $this->cant_envios = '';
        $this->tipo_contrato = [];
        $this->cliente_corporativo = '';
        $this->parametro_id = null;
        $this->tarifa = '';
        $this->precio_total = 0;
        $this->monto_divisa = null;
        $this->preview_bs = 0;
    }

    public function cerrar_estatus()
    {
        $this->modal_estatus = false;
    }

    public function cerrarMostrar()
    {
        $this->mostrar_contrato = false;
    }

    public function cerrar_pago()
    {
        $this->cancelar_cuota = false;
    }

    public function actualizar_estatus()
    {
        $contrato = ContratoCorporativo::where('contrato_corporativo_id', $this->contrato)->first();

        $contrato->update([

            'activo' => false,

            $contrato->save(),
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el contrato corporativo ({$contrato->contrato_corporativo_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Contrato actualizado correctamente!');
        $this->cerrar_estatus();
        $this->cerrarMostrar();
    }

    public function Actualizar()
    {
        $this->validate();

        if (!$this->estado || !$this->municipio || !$this->parroquia || !$this->codigo_postal) {
            $this->dispatch('alertSuccess2', message: 'Error, verifique que los campos de ubicacion esten correctamente llenados');
            return;
        } else {

            $cliente = ClienteCorporativo::where('cliente_corporativo_id', $this->cliente_actualizacion)->first();

            $cliente->update([

                'tipo_documento' => $this->tipo_documento,
                'numero_documento' => $this->documento,
                'razon_social' => $this->razon_social,
                'agente_autorizado' => $this->agente_autorizado,
                'estado_id' => $this->estado,
                'municipio_id' => $this->municipio,
                'parroquia_id' => $this->parroquia,
                'codigo_postal' => $this->codigo_postal,
                'latitud' => $this->latitud,
                'longitud' => $this->longitud,
                'telefono' => $this->telefono,
                'correo' => $this->correo,
                'direccion' => $this->direccion,

                $cliente->save(),


            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se actualizó el cliente corporativo ({$cliente->cliente_corporativo_id})",
            ]);
        }
        $this->dispatch('alertSuccess', message: 'Cliente actualizado exitosamente!');
        $this->dispatch('recargar');
    }

    public function submit()
    {
        $this->validate();
        DB::beginTransaction();

        try {
            $nuevo_cliente = ClienteCorporativo::create([
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'tipo_documento' => $this->tipo_documento,
                'numero_documento' => $this->documento,
                'razon_social' => $this->razon_social,
                'agente_autorizado' => $this->agente_autorizado,
                'estado_id' => $this->estado,
                'municipio_id' => $this->municipio,
                'parroquia_id' => $this->parroquia,
                'codigo_postal' => $this->codigo_postal,
                'latitud' => $this->latitud,
                'longitud' => $this->longitud,
                'telefono' => $this->telefono,
                'correo' => $this->correo,
                'direccion' => $this->direccion,
                'activo' => true,
                'parametro_id' => $this->tipo_divisa,
                'monto_divisa' => $this->monto_divisa
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró el cliente corporativo ({$nuevo_cliente->cliente_corporativo_id}) - {$this->razon_social}",
            ]);

            DB::commit();
            $this->dispatch('alertSuccess', message: 'Cliente registrado exitosamente!');
            $this->dispatch('recargar');
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error, verifique que todos los campos esten correctamente llenados!');
        }
    }


    public function crearContrato()
    {
        $this->fecha_inicio = Carbon::now()->format('Y-m-d');
        $this->fecha_fin = Carbon::parse($this->fecha_inicio)->addYear()->format('Y-m-d');

        $validator = Validator::make(
            $this->all(),
            [
                'cliente_corporativo' => 'required',
                'peso' => ['nullable', 'numeric', 'min:0'],
                'cant_envios' => ['nullable', 'integer', 'min:0', 'max:10000000'],
                'tarifa' => 'required',
                'parametro_id' => 'required|exists:parametro,parametro_id',
            ],
            [
                'parametro_id.required'  => 'Debe seleccionar una divisa.',
                'parametro_id.exists'    => 'La divisa seleccionada no es válida.',
                'cant_envios.max'        => 'La cantidad máxima de envíos no puede superar 10.000.000.',
                'cant_envios.integer'    => 'La cantidad de envíos debe ser un número entero.',
            ]
        );

        $validator->after(function ($validator) {
            if (
                (is_null($this->peso) || $this->peso <= 0) &&
                (is_null($this->cant_envios) || $this->cant_envios <= 0)
            ) {
                $validator->errors()->add('peso', 'Debes ingresar peso o cantidad de envíos con un valor positivo.');
                $validator->errors()->add('cant_envios', 'Debes ingresar peso o cantidad de envíos con un valor positivo.');
            }
        });

        if ($validator->fails()) {
            $this->setErrorBag($validator->errors());
            return; // detiene la ejecución para que se muestren errores
        }

        $verificar = ContratoCorporativo::where('cliente_corporativo_id', $this->cliente_corporativo)->where('activo', true)->first();
        if ($verificar) {
            $this->dispatch('alertSuccess2', message: 'El cliente aun posee un contrato activo!');
        } else {
            DB::beginTransaction();

            try {
                $contra = ContratoCorporativo::create([
                    'oficina_id' => $this->usuario['oficina_id'],
                    'usuario_id' => $this->usuario['id'],
                    'cliente_corporativo_id' => $this->cliente_corporativo,
                    'peso_contrato' => $this->peso_conver,
                    'cant_envios' => $this->cant_envios,
                    'tarifa' => $this->precio_total,        // monto en la divisa elegida
                    'peso_utilizado' => 0,
                    'fecha_inicio' => $this->fecha_inicio,
                    'fecha_fin' => $this->fecha_fin,
                    'activo' => true,
                    'tipo_contrato_id' => $this->tipo_contrato['tipo_contrato_id'],
                    'parametro_id' => $this->parametro_id,  // divisa elegida
                    'monto_divisa' => $this->precio_total,  // mismo monto en divisa
                ]);

                $fecha_inicio = Carbon::parse($this->fecha_inicio);
                $cuota = bcdiv((string)$this->precio_total, '12', 2);

                for ($i = 1; $i <= 12; $i++) {
                    ContratoCorporativoDetalle::create([
                        'cliente_corporativo_id' => $this->cliente_corporativo,
                        'contrato_corporativo_id' => $contra->contrato_corporativo_id,
                        'cuota' => $cuota,
                        'fecha_limite' => $fecha_inicio->copy()->addMonths($i)->format('Y-m-d'),
                        'cancelada' => false,
                    ]);
                }

                UsuarioSeguimiento::create([
                    'usuario_id'  => auth()->user()->id,
                    'accion'      => 'create',
                    'descripcion' => "Se generó el contrato corporativo ({$contra->contrato_corporativo_id}) para el cliente ({$this->cliente_corporativo})",
                ]);

                DB::commit();
                $this->dispatch('alertSuccess', message: 'Contrato Generado exitosamente!');
                $this->dispatch('recargar');
            } catch (\Exception $e) {
                // dd($e);
                DB::rollback();
                $this->dispatch('alertSuccess2', message: 'Ocurrio un error, verifique los datos!');
            }
        }
    }


    // ─── Direcciones ─────────────────────────────────────────────────────────

    public function abrirDirecciones($clienteId)
    {
        $cliente = ClienteCorporativo::find($clienteId);
        $this->dir_cliente_id    = $clienteId;
        $this->dir_cliente_razon = $cliente->razon_social;
        $this->modal_direcciones = true;
    }

    public function cerrarDirecciones()
    {
        $this->modal_direcciones    = false;
        $this->modal_crear_direccion = false;
        $this->resetDirForm();
    }

    public function abrirCrearDireccion()
    {
        $this->resetDirForm();
        $this->modal_crear_direccion = true;
    }

    public function cerrarCrearDireccion()
    {
        $this->modal_crear_direccion = false;
        $this->resetDirForm();
    }

    protected function resetDirForm()
    {
        $this->dir_alias          = '';
        $this->dir_persona        = '';
        $this->dir_estado         = '';
        $this->dir_municipio      = '';
        $this->dir_ciudad         = '';
        $this->dir_parroquia      = '';
        $this->dir_codigo_postal  = '';
        $this->dir_direccion      = '';
        $this->dir_telefono       = '';
        $this->dir_correo         = '';
        $this->dir_municipios     = [];
        $this->dir_ciudades       = [];
        $this->dir_parroquias     = [];
        $this->dir_codigos_postales = [];
    }

    public function updatedDirEstado($value)
    {
        $this->dir_municipio     = '';
        $this->dir_ciudad        = '';
        $this->dir_parroquia     = '';
        $this->dir_codigo_postal = '';
        $this->dir_municipios    = $value ? Municipio::where('estado_id', $value)->get() : [];
        $this->dir_ciudades      = [];
        $this->dir_parroquias    = [];
        $this->dir_codigos_postales = [];
    }

    public function updatedDirMunicipio($value)
    {
        $this->dir_ciudad        = '';
        $this->dir_parroquia     = '';
        $this->dir_codigo_postal = '';
        $this->dir_ciudades      = $value ? Ciudad::where('municipio_id', $value)->get() : [];
        $this->dir_parroquias    = $value ? Parroquia::where('municipio_id', $value)->get() : [];
        $this->dir_codigos_postales = [];
    }

    public function updatedDirParroquia($value)
    {
        $this->dir_codigo_postal    = '';
        $this->dir_codigos_postales = $value
            ? Sector::where('parroquia_id', $value)->distinct()->pluck('codigo_postal')->sort()
            : [];
    }

    public function guardarDireccion()
    {
        $this->validate([
            'dir_alias'         => 'required|string|max:50',
            'dir_persona'       => 'nullable|string|max:60',
            'dir_estado'        => 'required',
            'dir_municipio'     => 'required',
            'dir_ciudad'        => 'required',
            'dir_parroquia'     => 'required',
            'dir_codigo_postal' => 'required',
            'dir_direccion'     => 'required|string|max:255',
            'dir_telefono'      => ['nullable', new CodigosTelefono],
            'dir_correo'        => 'nullable|email|max:100',
        ], [
            'dir_alias.required'         => 'El alias es obligatorio.',
            'dir_estado.required'        => 'Seleccione un estado.',
            'dir_municipio.required'     => 'Seleccione un municipio.',
            'dir_ciudad.required'        => 'Seleccione una ciudad.',
            'dir_parroquia.required'     => 'Seleccione una parroquia.',
            'dir_codigo_postal.required' => 'Seleccione un código postal.',
            'dir_direccion.required'     => 'La dirección es obligatoria.',
            'dir_correo.email'           => 'El correo no tiene un formato válido.',
        ]);

        $nueva_dir = ClienteCorporativoDirecciones::create([
            'cliente_corporativo_id' => $this->dir_cliente_id,
            'alias'                  => $this->dir_alias,
            'persona'                => $this->dir_persona,
            'estado_id'              => $this->dir_estado,
            'municipio_id'           => $this->dir_municipio,
            'ciudad_id'              => $this->dir_ciudad,
            'parroquia_id'           => $this->dir_parroquia,
            'codigo_postal'          => $this->dir_codigo_postal,
            'direccion'              => $this->dir_direccion,
            'telefono'               => $this->dir_telefono ?: null,
            'correo'                 => $this->dir_correo ?: null,
            'activo'                 => true,
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se registró la dirección ({$nueva_dir->getKey()}) del cliente corporativo ({$this->dir_cliente_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Dirección registrada exitosamente.');
        $this->cerrarCrearDireccion();
    }

    public function toggleDireccion($id)
    {
        $dir = ClienteCorporativoDirecciones::find($id);
        $dir->activo = !$dir->activo;
        $dir->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se cambió el estatus de la dirección ({$id}) a " . ($dir->activo ? 'activa' : 'inactiva'),
        ]);
    }

    // ─── Fin Direcciones ──────────────────────────────────────────────────────

    public function reporte_excel()
    {
        $clientes = ClienteCorporativo::when($this->search, function ($query) {
            $query->where('razon_social', 'LIKE', '%' . $this->search . '%');
        })
            ->when($this->desde, function ($query) {
                $query->whereDate('created_at', '>=', $this->desde);
            })
            ->when($this->hasta, function ($query) {
                $query->whereDate('created_at', '<=', $this->hasta);
            })
            ->orderBy('cliente_corporativo_id', 'asc')
            ->get();

        return Excel::download(new ClientesCorporativosExport($clientes), 'clientes_corporativos.xlsx');
    }

    public function render()
    {
        $clientes = ClienteCorporativo::when($this->search, function ($query) {
            $query->where('razon_social', 'LIKE', '%' . $this->search . '%');
        })
            ->when($this->desde, function ($query) {
                $query->whereDate('created_at', '>=', $this->desde);
            })
            ->when($this->hasta, function ($query) {
                $query->whereDate('created_at', '<=', $this->hasta);
            })
            ->orderBy('cliente_corporativo_id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.clientes-corporativos.clientes-corporativos', compact('clientes'));
    }
}
