<?php

namespace App\Livewire\EnviosCorporativos;

use App\Models\Envio;

use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Sector;
use App\Models\Cliente;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Documento;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\EnvioInsumo;
use App\Models\Facturacion;
use App\Models\EnvioAlmacen;
use App\Models\InsumoUsuario;
use App\Models\FacturacionPago;
use Livewire\Attributes\Layout;
use App\Models\FacturacionEnvio;
use App\Models\ClienteCorporativo;
use App\Models\FacturacionDetalle;
use Illuminate\Support\Facades\DB;
use App\Models\ContratoCorporativo;
use App\Models\EnvioEncaminamiento;
use App\Models\ClienteCorporativoAutorizado;
use App\Models\ClienteCorporativoDirecciones;
use App\Models\UsuarioSeguimiento;
use App\Rules\CodigosTelefono;


#[Layout('layouts.app')]
class EnviosCorporativos extends Component
{
    public $peso, $estado, $municipio, $parroquia, $ciudad, $tipo_saca, $codigo_envio_creado, $documento_autorizado, $autorizado,
        $recoleccion, $cliente_corporativo, $representante, $contenido, $clase_correo, $codigo_rastreo, $documento_dest, $nombre,
        $nombre_dest, $estado_dest, $municipio_dest, $ciudad_dest, $parroquia_dest, $codigo_postal, $codigo_postal_dest, $direccion,
        $direccion_dest, $telefono, $telefono_dest, $correo, $correo_dest, $monto_pagado, $tipo_documento, $tipo_documento_dest,
        $metodo_pago_seleccionado, $monto, $pago_registrados, $cliente, $apellido, $apellido_dest,
        $servicioss;

    // Declarado aparte con type hint para que Livewire preserve los ceros a la izquierda
    public string $documento = '';

    public $parametro = ['Avenida' => '', 'Calle' => '', 'Edificio' => '', 'Nro Casa/Depa' => '', 'Punto de Ref' => ''];
    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $ciudades = [];
    public $codigos_postales = [];
    public $documentos = [];
    public $usuario = [];
    public $oficina = [];
    public $corporativos = [];
    public $inventarios = [];
    public $municipios_dest = [];
    public $parroquias_dest = [];
    public $ciudades_dest = [];
    public $codigos_postales_dest = [];
    public $clientes = [];
    public $personal_autorizado = [];
    public $contrato = [];
    public $insumo_sel = [];
    public $destinatario_existe = false;
    public $validar = false;

    // Direcciones guardadas del cliente
    public $direcciones_cliente = [];
    public $direccion_sel = '';  // ID de la dirección seleccionada
    public $modo_destino = 'manual'; // 'manual' | 'direccion'

    public function rules()
    {
        return [
            'cliente' => 'required_if:cliente_corporativo,true',
            'peso' => 'required|integer|min:1',
            'documento_autorizado' => 'required',
            'autorizado' => 'required',
            'contenido' => 'required|max:300',
            'nombre' => 'required',
            'apellido' => 'required_if:cliente_corporativo, false|max:20',
            'tipo_documento' => 'required',
            'representante' => 'required_if:cliente_corporativo, true',
            'documento' => 'required|digits_between:6,12',
            'telefono' => ['required', new CodigosTelefono],
            'correo' => 'required|email',
            'estado' => 'required_if:recoleccion, true',
            'municipio' => 'required_if:recoleccion, true',
            'parroquia' => 'required_if:recoleccion, true',
            'ciudad' => 'required_if:recoleccion,true',
            'codigo_postal' => 'required_if:recoleccion, true',
            'direccion' => 'required_if:recoleccion, true|max:200',
            'nombre_dest' => 'required_without:direccion_sel|nullable|regex:/^[A-Za-z ]+$/|max:50',
            'apellido_dest' => 'required_without:direccion_sel|nullable|max:20',
            'estado_dest' => 'required',
            'municipio_dest' => 'required',
            'parroquia_dest' => 'required',
            'ciudad_dest' => 'required',
            'codigo_postal_dest' => 'required',
            'tipo_documento_dest' => 'required_without:direccion_sel|nullable',
            'documento_dest' => 'required_without:direccion_sel|nullable',
            'telefono_dest' => ['required_without:direccion_sel', 'nullable', new CodigosTelefono],
            'correo_dest' => 'required_without:direccion_sel|nullable|email|max:50',
            'direccion_dest' => 'required|max:500'
        ];
    }



    protected $messages = [
        'cliente.required_if' => 'Debe seleccionar un cliente corporativo',
        'tipo_documento_dest.required_without' => 'El campo es obligatorio',
        'documento_dest.required_without' => 'El campo es obligatorio',
        'nombre_dest.required_without' => 'El campo es obligatorio',
        'estado_dest.required' => 'El campo es obligatorio',
        'ciudad_dest.required' => 'El campo es obligatorio',
        'parroquia_dest.required' => 'El campo es obligatorio',
        'codigo_postal_dest.required' => 'El campo es obligatorio',
        'direccion_dest.required' => 'El campo es obligatorio',
        'direccion_dest.max' => 'Maximo de 200 caracteres',
        'telefono_dest.required_without' => 'El campo es obligatorio',
        'telefono_dest.regex' => 'Debe estar en un formato valido',
        'correo_dest.required_without' => 'El campo es obligatorio',
        'correo_dest.email' => 'El formato del correo debe ser valido',
        'apellido_dest.required_without' => 'El campo es obligatorio',
    ];



    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->estados = Estado::where('pais_id', 90)->get();
        $this->documentos = Documento::all();
        $this->inventarios = InsumoUsuario::where('usuario_id', $this->usuario['id'])->orderBy('insumo_id')->get();
        $this->clientes = ClienteCorporativo::where('activo', true)->get();

        $this->obtenerOrigen();
    }


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function obtenerOrigen()
    {
        $this->estado = $this->oficina['estado_id'];
        $this->municipio = $this->oficina['municipio_id'];
        $this->parroquia = $this->oficina['parroquia_id'];
        $this->codigo_postal = $this->oficina['codigo_ubicacion'];
        $this->direccion = $this->oficina['direccion'];
    }


    public function updatedCliente()
    {
        if ($this->cliente) {
            $cliente_corp = ClienteCorporativo::where('cliente_corporativo_id', $this->cliente)->first();

            $this->nombre = $cliente_corp->razon_social;
            $this->representante = $cliente_corp->agente_autorizado;
            $this->tipo_documento = $cliente_corp->tipo_documento;
            $this->documento = (string) $cliente_corp->numero_documento;
            $this->telefono = $cliente_corp->telefono;
            $this->correo = $cliente_corp->correo;

            $this->contrato = ContratoCorporativo::where('cliente_corporativo_id', $this->cliente)->where('activo', true)->latest()->first();

            $this->direcciones_cliente = ClienteCorporativoDirecciones::with(['estado', 'municipio', 'ciudad', 'parroquia'])
                ->where('cliente_corporativo_id', $this->cliente)
                ->where('activo', true)
                ->get()
                ->toArray();
        } else {
            $this->contrato = [];
            $this->direcciones_cliente = [];
        }

        $this->direccion_sel = '';
        $this->modo_destino = 'manual';
    }

    public function cambiarModoDestino($modo)
    {
        $this->modo_destino = $modo;

        // Al cambiar entre manual / dirección guardada, limpiar la selección y los campos de ubicación
        $this->direccion_sel = '';
        $this->estado_dest = '';
        $this->municipio_dest = '';
        $this->ciudad_dest = '';
        $this->parroquia_dest = '';
        $this->codigo_postal_dest = '';
        $this->direccion_dest = '';
        $this->telefono_dest = '';
        $this->correo_dest = '';
        $this->municipios_dest = [];
        $this->ciudades_dest = [];
        $this->parroquias_dest = [];
        $this->codigos_postales_dest = [];
    }

    public function updatedDireccionSel()
    {
        if (!$this->direccion_sel) {
            return;
        }

        $dir = ClienteCorporativoDirecciones::find($this->direccion_sel);

        if (!$dir) return;

        // Cargar los arrays directamente (sin pasar por los hooks que resetean hijos)
        $this->municipios_dest       = Municipio::where('estado_id', $dir->estado_id)->get();
        $this->parroquias_dest       = Parroquia::where('municipio_id', $dir->municipio_id)->get();
        $this->ciudades_dest         = Ciudad::where('municipio_id', $dir->municipio_id)->get();
        $this->codigos_postales_dest = Sector::where('parroquia_id', $dir->parroquia_id)->distinct()->pluck('codigo_postal')->sort();

        // Asignar valores como string (los values de los <option> son strings)
        $this->estado_dest        = (string) $dir->estado_id;
        $this->municipio_dest     = (string) $dir->municipio_id;
        $this->ciudad_dest        = (string) $dir->ciudad_id;
        $this->parroquia_dest     = (string) $dir->parroquia_id;
        $this->codigo_postal_dest = (string) $dir->codigo_postal;
        $this->direccion_dest     = $dir->direccion;

        if ($dir->telefono) {
            $this->telefono_dest = $dir->telefono;
        }

        if ($dir->correo) {
            $this->correo_dest = $dir->correo;
        }
    }

    public function updatedParametro()
    {
        $this->direccion_dest = '';
        foreach ($this->parametro as $key => $direccion_dest) {
            if ($direccion_dest) {
                if (strpos($this->direccion_dest, $key) === false) {
                    $this->direccion_dest .= $key . ': ' . $direccion_dest . ', ';
                }
            }
        }
    }

    public function updatedPeso()
    {
        $this->peso = preg_replace('/[^0-9]/', '', (string)$this->peso);
    }

    public function updatedDocumentoAutorizado()
    {
        if ($this->documento_autorizado) {

            $this->personal_autorizado = ClienteCorporativoAutorizado::where('documento', $this->documento_autorizado)->first();

            if ($this->personal_autorizado) {
                $this->autorizado = $this->personal_autorizado['nombre'];
            } else {
                $this->autorizado = '';
            }
        } else {

            $this->personal_autorizado = [];
            $this->autorizado = '';
        }
    }

    public function updatedDocumentoDest()
    {
        if ($this->documento_dest == '') {
            return;
        } else {
            $cliente = Cliente::where('numero_documento', $this->documento_dest)->first();

            if ($cliente) {
                $this->destinatario_existe = true;
                $this->nombre_dest = $cliente->nombre;
                $this->apellido_dest = $cliente->apellido;
                $this->tipo_documento_dest = $cliente->tipo_documento;
                $this->telefono_dest = $cliente->telefono;
                $this->correo_dest = $cliente->correo;
            } else {
                $this->destinatario_existe = false;
                return;
            }
        }
    }


    public function updatedEstado()
    {
        if ($this->estado == '') {
            $this->municipios = [];
        } else {
            $this->municipios = Municipio::where('estado_id', $this->estado)->get();
        }
    }

    public function updatedEstadoDest()
    {
        // Resetear los hijos de la cascada para evitar valores huérfanos
        $this->municipio_dest = '';
        $this->ciudad_dest = '';
        $this->parroquia_dest = '';
        $this->codigo_postal_dest = '';
        $this->parroquias_dest = [];
        $this->ciudades_dest = [];
        $this->codigos_postales_dest = [];

        if ($this->estado_dest == '') {
            $this->municipios_dest = [];
        } else {
            $this->municipios_dest = Municipio::where('estado_id', $this->estado_dest)->get();
        }
    }


    public function updatedMunicipio()
    {
        if ($this->municipio == '') {
            $this->parroquias = [];
            $this->ciudades = [];
        } else {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio)->get();
            $this->ciudades = Ciudad::where('municipio_id', $this->municipio)->get();
        }
    }

    public function updatedMunicipioDest()
    {
        // Resetear los hijos de la cascada
        $this->ciudad_dest = '';
        $this->parroquia_dest = '';
        $this->codigo_postal_dest = '';
        $this->codigos_postales_dest = [];

        if ($this->municipio_dest == '') {
            $this->parroquias_dest = [];
            $this->ciudades_dest = [];
        } else {
            $this->parroquias_dest = Parroquia::where('municipio_id', $this->municipio_dest)->get();
            $this->ciudades_dest = Ciudad::where('municipio_id', $this->municipio_dest)->get();
        }
    }

    public function updatedParroquia()
    {
        if ($this->parroquia == '') {
            $this->codigos_postales = [];
        } else {
            $this->codigos_postales = Sector::where('parroquia_id', $this->parroquia)->distinct()->pluck('codigo_postal')->sort();
        }
    }

    public function updatedParroquiaDest()
    {
        // Resetear el hijo de la cascada
        $this->codigo_postal_dest = '';

        if ($this->parroquia_dest == '') {
            $this->codigos_postales_dest = [];
        } else {
            $this->codigos_postales_dest = Sector::where('parroquia_id', $this->parroquia_dest)->distinct()->pluck('codigo_postal')->sort();
        }
    }


    private function crearCodigoEnvioNacional()
    {
        if ($this->usuario['oficina_id']) {

            $office = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
            $cod_origen = $office->codigo;

            if ($this->servicioss == 2) {
                if (empty($this->info_registro_apartado)) {
                    $this->dispatch('alertSuccess2', message: 'Error, verifique que el apartado postal sea correcto');
                    return;
                } else {
                    $office_estado = Oficina::where('oficina_id', $this->info_registro_apartado['oficina_id'])->pluck('estado_id')->first();
                    $office_dest = Oficina::where('estado_id', $office_estado)->where('tipo_oficina_id', 4)->where('externa', false)->first();
                }
            } else {
                if ($this->estado_dest == 2 || $this->estado_dest == 24) {
                    $office_dest = Oficina::where('estado_id', 1)->where('tipo_oficina_id', 4)->where('externa', false)->first();
                } elseif ($this->estado_dest == 20) {
                    $office_dest = Oficina::where('estado_id', 21)->where('tipo_oficina_id', 4)->where('externa', false)->first();
                } else {
                    $office_dest = Oficina::where('estado_id', $this->estado_dest)
                        ->where('tipo_oficina_id', 4)
                        ->where('externa', false)
                        ->first();
                }
            }

            if (!$office_dest) {
                $nuevo_correlativo = $this->generarCorrelativoSimplificado();
                $this->codigo_envio_creado = $cod_origen . $nuevo_correlativo . 'SO';
                return;
            }

            $cod_destino = $office_dest->codigo;

            // Validar que el código de la oficina destino tenga el formato esperado
            if (!preg_match('/^(OP|CP|CO|CI|EX)\d{3}$/', $cod_destino)) {
                $estado_nombre = $office_dest->estado->nombre ?? 'Desconocido';
                $this->dispatch('alertSuccess2', message: "Error: la oficina '{$office_dest->nombre}' (ID: {$office_dest->oficina_id}) del estado {$estado_nombre} tiene un código inválido ({$cod_destino}). Contacte al administrador.");
                return;
            }

            // Verificar si la combinación ya existe
            $envio_existente = Envio::where('codigo_envio', 'like', $cod_origen . $cod_destino . '%')
                ->orderBy('envio_id', 'desc')
                ->first();

            if ($envio_existente) {
                // Extraer el correlativo del código de envío existente
                preg_match('/' . preg_quote($cod_origen) . preg_quote($cod_destino) . '(\d+)(?=\b)/', $envio_existente->codigo_envio, $matches);

                // Si se encuentra un correlativo, extraerlo; de lo contrario, iniciar con 0
                $ultimo_correlativo = isset($matches[1]) ? (int)$matches[1] : 0;

                // Incrementar el correlativo y asegurarse de que tenga 9 dígitos
                $nuevo_correlativo = str_pad($ultimo_correlativo + 1, 9, '0', STR_PAD_LEFT);
            } else {
                $nuevo_correlativo = '000000001'; // Primer correlativo
            }

            $this->codigo_envio_creado =  $cod_origen . $cod_destino . $nuevo_correlativo;
        } else {
            $this->dispatch('alertSuccess2', message: 'Error, el usuario no esta asignado a ninguna oficina');
            return;
        }
    }

    private function generarCorrelativoSimplificado()
    {
        // Obtener el último correlativo registrado en la tabla 'envios'
        $envio_existente = Envio::orderBy('envio_id', 'desc')->first();

        if ($envio_existente) {
            // Extraer el correlativo del último código de envío
            preg_match('/\d{9}/', $envio_existente->codigo_envio, $matches);
            $ultimo_correlativo = isset($matches[0]) ? (int)$matches[0] : 0;
        } else {
            // Si no hay envíos existentes, comenzamos desde 0
            $ultimo_correlativo = 0;
        }

        // Generar el nuevo correlativo
        $nuevo_correlativo = str_pad($ultimo_correlativo + 1, 9, '0', STR_PAD_LEFT);

        return $nuevo_correlativo; // Devolver el nuevo correlativo
    }


    private function reducir_peso()
    {
        $nuevo_peso = $this->contrato['peso_utilizado'] + $this->peso;

        ContratoCorporativo::where('contrato_corporativo_id', $this->contrato['contrato_corporativo_id'])->update([
            'peso_utilizado' => $nuevo_peso,
        ]);
    }

    private function reducir_envios()
    {
        $nuevo_envio = $this->contrato['cant_envios_utilizados'] + 1;

        ContratoCorporativo::where('contrato_corporativo_id', $this->contrato['contrato_corporativo_id'])->update([
            'cant_envios_utilizados' => $nuevo_envio,
        ]);
    }


    public function submit()
    {
        $this->validate();

        if (!$this->contrato) {
            $this->dispatch('alertSuccess2', message: 'Este cliente no posee un contrato valido');
            return;
        }

        $tipo = $this->contrato['tipo_contrato_id'];
        $peso_contrato = $this->contrato['peso_contrato'] ?? 0;
        $peso_utilizado = $this->contrato['peso_utilizado'] ?? 0;
        $peso_disponible = $peso_contrato - $peso_utilizado;

        $envio_contrato = $this->contrato['cant_envios'] ?? 0;
        $envios_utilizados = $this->contrato['cant_envios_utilizados'] ?? 0;
        $envios_disponibles = $envio_contrato - $envios_utilizados;

        DB::beginTransaction();

        try {
            switch ($tipo) {
                case 1: // Solo peso
                    if ($peso_disponible >= $this->peso) {
                        $this->reducir_peso();
                        $this->crearCodigoEnvioNacional();
                        $this->crear_envio();
                    } else {
                        throw new \Exception('El contrato no dispone de peso suficiente para realizar el envío');
                    }
                    break;

                case 2: // Solo envíos
                    if ($envios_disponibles >= 1) {
                        $this->reducir_envios();
                        $this->crearCodigoEnvioNacional();
                        $this->crear_envio();
                    } else {
                        throw new \Exception('El contrato no dispone de envíos disponibles');
                    }
                    break;

                default: // Peso y envíos
                    if ($peso_disponible >= $this->peso && $envios_disponibles >= 1) {
                        $this->reducir_peso();
                        $this->reducir_envios();
                        $this->crearCodigoEnvioNacional();
                        $this->crear_envio();
                    } else {
                        throw new \Exception('El contrato no dispone de peso y/o envíos suficientes para realizar el envío');
                    }
                    break;
            }

            DB::commit();
            $this->dispatch('alertSuccess', message: 'Envio Creado exitosamente!');
            $this->contrato = ContratoCorporativo::where('contrato_corporativo_id', $this->contrato['contrato_corporativo_id'])->first();
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Error: ' . $e->getMessage());
        }
    }



    public function crear_envio()
    {
        // Solo guardar el cliente destinatario si se ingresó manualmente (no si se eligió una dirección guardada)
        if ($this->destinatario_existe == 0 && empty($this->direccion_sel) && !empty($this->documento_dest)) {
            Cliente::create([
                'numero_documento' => $this->documento_dest,
                'nombre' => $this->nombre_dest,
                'apellido' => $this->apellido_dest,
                'tipo_documento' => $this->tipo_documento_dest,
                'telefono' => $this->telefono_dest,
                'correo' => $this->correo_dest,
            ]);
        }

        DB::beginTransaction();

        try {

            $envio = Envio::create([
                'servicio_id' => 24,
                'tipo_envio' => 'nacional',
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'nombre_rem' => $this->nombre,
                'apellido_rem' => $this->representante,
                'tipo_documento_rem' => $this->tipo_documento,
                'documento_rem' => $this->documento,
                'codigo_postal_rem' => $this->codigo_postal,
                'estado_rem' => $this->estado,
                'municipio_rem' => $this->municipio,
                'parroquia_rem' => $this->parroquia,
                'ciudad_rem' => $this->ciudad ?? 'N/A',
                'direccion_rem' => $this->direccion,
                'correo_rem' => $this->correo,
                'telefono_rem' => $this->telefono,
                'nombre_dest' => $this->nombre_dest,
                'apellido_dest' => $this->apellido_dest,
                'tipo_documento_dest' => $this->tipo_documento_dest,
                'documento_dest' => $this->documento_dest,
                'codigo_postal_dest' => $this->codigo_postal_dest,
                'continente_dest' => null,
                'pais_dest' => null,
                'estado_dest' => $this->estado_dest,
                'municipio_dest' => $this->municipio_dest,
                'parroquia_dest' => $this->parroquia_dest,
                'ciudad_dest' => $this->ciudad_dest ?? null,
                'direccion_dest' => $this->direccion_dest,
                'tlf_dest' => $this->telefono_dest,
                'correo_dest' => $this->correo_dest,
                'servicio_expreso' => null,
                'peso' => $this->peso,
                'coste' => 0,
                'contenido' => $this->contenido,
                'apartado_postal' => null,
                'codigo_envio' => $this->codigo_envio_creado,
                'devolucion' => false,
                'descubierto' => false,
                'tipo_saca_id' => 16,
                'coste_sin_iva' => 0,
                'contrato_corporativo_id' => $this->contrato['contrato_corporativo_id'] ?? null,
                'cliente_corporativo_autorizado_id' => $this->personal_autorizado['cliente_corporativo_autorizado_id'] ?? null
            ]);

            if ($this->insumo_sel) {
                foreach ($this->insumo_sel as $insumo) {
                    EnvioInsumo::create([
                        'envio_id' => $envio->envio_id,
                        'insumo_id' => $insumo,
                        'coste' => 0
                    ]);
                };
            }

            if ($this->insumo_sel) {
                // Iterar sobre los insumos seleccionados y descontar una unidad
                foreach ($this->insumo_sel as $insumo_id) {
                    // Buscar la relación en la tabla pivote y restar una unidad
                    $inventario_insumo = InsumoUsuario::where('oficina_id', $this->usuario['oficina_id'])
                        ->where('usuario_id', $this->usuario['id'])->where('insumo_id', $insumo_id)->first();

                    if ($inventario_insumo && $inventario_insumo->cantidad > 0) {

                        $inventario_insumo->cantidad -= 1;

                        $inventario_insumo->save();
                    }
                }
                $this->insumo_sel = [];
            }

            EnvioEncaminamiento::create([
                'envio_id' => $envio->envio_id,
                'oficina_id' => $envio->oficina_id,
                'usuario_id' => $this->usuario['id'],
                'estatus_id'  => 1,
                'devolucion' => false,

            ]);

            EnvioAlmacen::create([
                'oficina_id' => $this->usuario['oficina_id'],
                'envio_id' => $envio->envio_id,
                'codigo' => $envio->codigo_envio,
                'saca_id' => null,
                'estatus' => true,
                'Entrada' => now()->toDateString(),
                'Salida' => null,
            ]);


            $facturacion = Facturacion::create([
                'oficina_id' => $this->usuario['oficina_id'],
                'nombre' => $envio->nombre_rem,
                'apellido' => $envio->apellido_rem,
                'tipo_documento' => $envio->tipo_documento_rem,
                'documento' => $envio->documento_rem,
                'direccion' => $envio->direccion_rem,
                'monto_total' => $envio->coste,
                'iva' => 0,
            ]);

            FacturacionPago::create([
                'facturacion_id' => $facturacion->facturacion_id,
                'tipo_pago_id' => 6,
                'monto' => 0,
            ]);


            FacturacionEnvio::create([
                'facturacion_id' => $facturacion->facturacion_id,
                'envio_id' => $envio->envio_id,
                'usuario_id' => $envio->usuario_id,
                'oficina_id' => $envio->oficina_id,
                'servicio_id' => $envio->servicio_id
            ]);

            FacturacionDetalle::create([
                'servicio_id' => $envio->servicio_id,
                'facturacion_id' => $facturacion->facturacion_id,
                'monto' => 0
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se creó el envío corporativo ({$envio->envio_id}) con código ({$envio->codigo_envio})",
            ]);

            DB::commit();

            $this->dispatch('alertSuccess', message: 'Envio Creado exitosamente!');
            $this->dispatch('envio_registrado');
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en el registro, verifique los datos e intente de nuevo');
            return;
            // Si ocurre un error, revertimos todos los cambios
        }
    }


    public function render()
    {
        return view('livewire.envios-corporativos.envios-corporativos');
    }
}
