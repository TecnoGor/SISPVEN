<?php

namespace App\Livewire\Envios;
use App\Models\Pais;
use App\Models\Envio;
use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Insumo;
use App\Models\Sector;
use App\Models\Cliente;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Servicio;
use App\Models\TipoPago;
use App\Models\TipoSaca;
use App\Models\Documento;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Continente;
use App\Models\Facturacion;
use App\Models\EnvioAlmacen;
use App\Models\FacturacionPago;
use Livewire\Attributes\Layout;
use App\Models\FacturacionEnvio;
use App\Models\RegistroApartado;

use App\Models\ClienteCorporativo;
use App\Models\FacturacionDetalle;
use Illuminate\Support\Facades\DB;
use App\Models\EnvioEncaminamiento;
use App\Models\TarifaNacionalRango;
use Illuminate\Support\Facades\Log;
use App\Models\CodigoApartadoPostal;
use App\Models\FacturacionTarifaEnvio;
use App\Models\TarifaNacionalConcepto;
use App\Models\TarifaExpresoBolivariano;
use App\Models\TarifaInternacionalRango;
use App\Models\TarifaInternacionalConcepto;
use App\Models\UsuarioSeguimiento;
use App\Livewire\GenerarTermica\GenerarTermica;

#[Layout('layouts.app')]
class EnviosForm extends Component
{
    public $nombre_rem, $apellido_rem, $tipo_documento_rem, $documento_rem, $codigo_postal_rem, $estadoss, $municipioss,
    $ciudades_rem, $parroquiass, $direccion_rem, $tlf_rem, $correo_rem, $showinter;

    public $nombre_dest, $apellido_dest, $tipo_documento_dest, $documento_dest, $codigo_postal_dest, $continentess, $paiss,
    $estadoss_dest, $estados_dest_inter, $municipioss_dest, $ciudadess_dest, $parroquiass_dest, $tlf_dest, $correo_dest;

    public $mensaje, $servicioss, $seb, $taquilla_postal, $peso, $peso_internacional, $costo, $contenido, $tarifa,
    $cliente_existe, $destinatario_existe, $monto, $monto_pagado, $metodo_pago_seleccionado, $codigo_envio,
    $precio_total, $cantidad_tarjetas_postales, $continente_id, $continente_grupo, $cantidad_palabras, $telegrama,
    $tipo_envio_expreso, $envio_expreso_b, $codigo_envio_creado, $correlativo, $TF, $tipo_tarifa, $tarifa_selec, $total_subser,
    $recoleccion, $recoleccion_coste, $recol_mensj, $codigo_apartado_postal, $correlativo_apartado, $aprobacion_apartado,
    $tipo_saca, $apartado, $oficina_dest_id, $codigo, $opt_dest;

    public $parametro = ['Avenida' => '', 'Calle' => '', 'Edificio' => '', 'Nro Casa/Depa' => ''];
    public $direccion_dest = '';
    public $certificado = false;
    public $mostrar_subservicio_inter = true;
    public $mostrar_subservicio = true;
    public $envio_seleccionado = 'nacional';
    public $documentos = [];
    public $clientes_corp = [];
    public $cliente_corp = [];
    public $corporativo = false;
    public $sacas = [];
    public $continentes = [];
    public $paises = [];
    public $estados = [];
    public $estadoss_dest_inter = [];
    public $municipios = [];
    public $municipios_dest = [];
    public $ciudades = [];
    public $ciudades_dest = [];
    public $parroquias = [];
    public $parroquias_dest = [];
    public $servicios = [];
    public $servicio_tarifa =[];
    public $codigos_postales_rem = [];
    public $codigos_postales_dest = [];
    public $servicios_filtrados = [];
    public $metodos_pago = [];
    public $pagos = [];
    public $pago_registrados = [];
    public $pago_eliminar = '';
    public $usuario = [];
    public $tarifas = [];
    public $tarifa_encon = [];
    public $tarifa_inter_encon = [];
    public $subservicios = [];
    public $subservicio_encon = [];
    public $subser = [];
    public $subser_pretotal;
    public $subservicios_inter = [];
    public $subservicio_inter_encon = [];
    public $subser_inter = [];
    public $total_subser_inter;
    public $estado_or = [];
    public $ciudad_or = [];
    public $estado_dest = [];
    public $ciudad_dest = [];
    public $info_registro_apartado = [];
    public $deshabilitado = false;
    public $iva = 0;
    public $subser_iva = 0;
    public $iva_recolec = 0;
    public $total_pagar = 0;
    public $recoleccion_monto = 0;
    public $apartados_postales;
    public $insumos = [];
    public $insumo_sel = [];
    public $insumo_selec = [];
    public $insumo_pretotal = 0;
    public $opt = [];
    public $apartados = [];


    protected $messages = [
        'contenido.required' => 'El contenido del envio es obligatorio',
        'contenido.max' => 'El maximo de caracteres es 100',
        'peso.required' => 'El peso es obligatorio',
        'codigo_apartado_postal' => 'El codigo es obligatorio',

        'tipo_documento_rem.required' => 'El tipo de documento es obligatorio',
        'documento_rem.required' => 'El documento es obligatorio',
        'nombre_rem.required' => 'El nombre del remitente es obligatorio',
        'nombre_rem.min' => 'El nombre debe tener un minimo de 3 caracteres',
        'nombre_rem.max' => 'El nombre debe tener un maximo de 20 caracteres',
        'apellido_rem.min' => 'El apellido debe tener un minimo de 3 caracteres',
        'apellido_rem.max' => 'El apellido debe tener un maximo de 20 caracteres',
        'estadoss.required' => 'El estado es obligatorio',
        'municipioss.required' => 'El municipio es obligatorio',
        'parroquiass.required' => 'La parroquia es obligatoria',
        'ciudades_rem.required' => 'La ciudad es obligatoria',
        'codigo_postal_rem.required' => 'El codigo postal es obligatorio',
        'direccion_rem.required' => 'La direccion es obligatoria',
        'direccion_rem.max' => 'La direccion debe tener un maximo de 150 caracteres',
        'tlf_rem.required' => 'El telefono es obligatorio',
        'tlf_rem.regex' => 'El campo debe contener al menos 11 digitos o un + seguido de 10 digitos',
        'correo_rem.required' => 'El correo es obligatorio',

        'tipo_documento_dest.required' => 'El tipo de documento es obligatorio',
        'documento_dest.required' => 'El documento es obligatorio',
        'nombre_dest.required' => 'El nombre del remitente es obligatorio',
        'nombre_dest.min' => 'El nombre debe tener un minimo de 3 caracteres',
        'nombre_dest.max' => 'El nombre debe tener un maximo de 20 caracteres',
        'apellido_dest.min' => 'El apellido debe tener un minimo de 3 caracteres',
        'apellido_dest.max' => 'El apellido debe tener un maximo de 20 caracteres',
        'estadoss_dest.required' => 'El estado es obligatorio',
        'municipioss_dest.required' => 'El municipio es obligatorio',
        'parroquiass_dest.required' => 'La parroquia es obligatoria',
        'ciudades_dest.required' => 'La ciudad es obligatoria',
        'codigo_postal_dest.required' => 'El codigo postal es obligatorio',
        'direccion_dest.required' => 'La direccion es obligatoria',
        'direccion_dest.max' => 'La direccion debe tener un maximo de 150 caracteres',
        'telefono_dest.required' => 'El telefono es obligatorio',
        'telefono_dest.regex' => 'El campo debe contener al menos 11 digitos o un + seguido de 10 digitos',
        'correo_dest.required' => 'El correo es obligatorio',

        'metodo_pago_seleccionado.required' => 'Debe seleccionar un metodo de pago',
        'monto.required' => 'El monto es obligatorio',
        'monto.regex' => 'El monto debe ser de de tipo numerico',
        'pago_registrados.required' => 'Debe seleccionar un metodo de pago realizado'
    ];


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function habilitar()
    {
        $this->validate($this->rules());

        if($this->servicioss == 2){
            if($this->peso >= 500){
                if ($this->monto_pagado > 0 && $this->monto_pagado >=  $this->total_pagar) {
                    $this->crearCodigoEnvioNacional();
                    $this->codigo_envio = $this->codigo_envio_creado;
                    $this->submit();
                }else{
                    $this->dispatch('alertSuccess2', message: 'El pago realizado no coincide con el monto a pagar');
                    return;
                }
            }else{
                $this->crearCodigoEnvioNacional();
                $this->codigo_envio = $this->codigo_envio_creado;
                $this->submit();
                }
        }else{

            if ($this->monto_pagado > 0 && $this->monto_pagado >=  $this->total_pagar) {
                if($this->envio_seleccionado === 'nacional'){
                    $this->crearCodigoEnvioNacional();
                }else{
                    $this->codigo_envio_creado = $this->codigo;
                }
                $this->codigo_envio = $this->codigo_envio_creado;
                $this->submit();
            } else {
                $this->dispatch('alertSuccess2', message: 'El pago realizado no coincide con el monto a pagar');
            }
        }
    }

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->sacas = TipoSaca::all();
        $this->documentos = Documento::all();
        $this->continentes = Continente::all();
        $this->estados = Estado::all();
        $this->servicios = Servicio::all()->toArray();
        $this->metodos_pago = TipoPago::all();
        $this->insumos = Insumo::all();
        $this->subservicios = TarifaNacionalConcepto::where('servicios_id', 7)->whereIn('tarifa_conceptos_id', [14])->get();
        $this->clientes_corp = ClienteCorporativo::all();

        $info_oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->estadoss = $info_oficina->estado_id;
        $this->municipioss = $info_oficina->municipio_id;
        $this->parroquiass = $info_oficina->parroquia_id;
        $this->direccion_rem = $info_oficina->direccion;
        $this->codigo_postal_rem = $info_oficina->codigo_ubicacion;

        $this->filtrarServicio();
        $this->updatedMunicipioss($info_oficina->municipio_id);
    }


    public function borrar()
    {
        $this->tarifa_encon = []; $this->subservicio_encon = []; $this->insumo_sel = [];
        $this->subservicio_inter_encon = []; $this->codigo = ''; $this->recoleccion = false;
        $this->peso = ''; $this->contenido = ''; $this->nombre_rem = ''; $this->apellido_rem = '';
        $this->tipo_documento_rem = ''; $this->documento_rem = ''; $this->cliente_corp = ''; $this->estadoss = '';
        $this->municipioss = ''; $this->parroquiass = ''; $this->ciudades_rem = ''; $this->codigo_postal_rem = '';
        $this->direccion_rem = ''; $this->tlf_rem = ''; $this->correo_rem = ''; $this->nombre_dest = '';
        $this->apellido_dest = ''; $this->tipo_documento_dest = ''; $this->documento_dest = ''; $this->continentess = '';
        $this->paiss = ''; $this->estados_dest_inter = ''; $this->estadoss_dest = ''; $this->municipioss_dest = '';
        $this->parroquiass_dest = ''; $this->ciudadess_dest = ''; $this->codigo_postal_dest = ''; $this->opt_dest = '';
        $this->codigo_apartado_postal = ''; $this->parametro = ['Avenida' => '', 'Calle' => '', 'Edificio' => '', 'Nro Casa/Depa' => '']; 
        $this->direccion_dest = ''; $this->tlf_dest = ''; $this->correo_dest = ''; $this->metodo_pago_seleccionado = [];
        $this->monto = ''; $this->pago_registrados = []; $this->monto_pagado = ''; $this->pagos = [];
    }

    public function updatedCorporativo()
    {
        if($this->corporativo == false){
            $this->recoleccion = false;
        }else{
            $this->apartado = false;
        }
    }


    public function rules()
    {
        switch ($this->servicioss) {
            case 1:
            case 9:
                if($this->corporativo && $this->recoleccion){
                    $reglas = ['contenido' => 'required|max:300', 'tipo_documento_rem' => 'required', 'documento_rem' => 'required',
            'nombre_rem' => 'required|min:3|max:20', 'apellido_rem' =>'min:3|max:20', 'estadoss' => 'required', 'municipioss' => 'required',
            'parroquiass' => 'required', 'ciudades_rem' => 'required', 'codigo_postal_rem' => 'required', 'direccion_rem' =>'required|max:150',
            'tlf_rem' => 'required|regex:/^(\+?\d{10,11})$/', 'correo_rem' => 'required', 'tipo_documento_dest' => 'required',
            'documento_dest' => 'required', 'nombre_dest' => 'required', 'apellido_dest' => 'required', 'estadoss_dest' => 'required',
            'municipioss_dest' => 'required', 'parroquiass_dest' => 'required', 'ciudadess_dest' => 'required', 'codigo_postal_dest' => 'required',
            'direccion_dest' => 'required', 'tlf_dest' => 'required|regex:/^(\+?\d{10,11})$/', 'correo_dest' => 'required'];
            break;
                }elseif($this->corporativo){
                $reglas = ['contenido' => 'required|max:300', 'tipo_documento_rem' => 'required', 'documento_rem' => 'required',
            'nombre_rem' => 'required|min:3|max:20', 'apellido_rem' =>'min:3|max:20', 'tlf_rem' => 'required|regex:/^(\+?\d{10,11})$/',
            'correo_rem' => 'required', 'tipo_documento_dest' => 'required', 'documento_dest' => 'required', 'nombre_dest' => 'required',
            'apellido_dest' => 'required', 'estadoss_dest' => 'required', 'municipioss_dest' => 'required', 'parroquiass_dest' => 'required',
            'ciudadess_dest' => 'required', 'codigo_postal_dest' => 'required', 'direccion_dest' => 'required',
            'tlf_dest' => 'required|regex:/^(\+?\d{10,11})$/', 'correo_dest' => 'required'];
            break;

            }elseif($this->apartado){
                $reglas = ['contenido' => 'required|max:300', 'tipo_documento_rem' => 'required', 'documento_rem' => 'required',
                'nombre_rem' => 'required|min:3|max:20', 'apellido_rem' =>'min:3|max:20', 'tlf_rem' => 'required|regex:/^(\+?\d{10,11})$/',
                'correo_rem' => 'required', 'codigo_apartado_postal' => 'required'];
            break;
            }else{
                $reglas = ['contenido' => 'required|max:300', 'tipo_documento_rem' => 'required', 'documento_rem' => 'required',
            'nombre_rem' => 'required|min:3|max:20', 'apellido_rem' =>'min:3|max:20', 'tlf_rem' => 'required|regex:/^(\+?\d{10,11})$/',
            'correo_rem' => 'required', 'tipo_documento_dest' => 'required', 'documento_dest' => 'required', 'nombre_dest' => 'required',
            'apellido_dest' => 'required', 'estadoss_dest' => 'required', 'municipioss_dest' => 'required', 'parroquiass_dest' => 'required',
            'ciudadess_dest' => 'required', 'codigo_postal_dest' => 'required', 'direccion_dest' => 'required',
            'tlf_dest' => 'required|regex:/^(\+?\d{10,11})$/', 'correo_dest' => 'required'];
            break;
            }
            case 14:
            case 15:
            case 17:
            case 18:
            case 19:
            case 21:    
                $reglas = ['contenido' => 'required|max:300', 'tipo_documento_rem' => 'required', 'documento_rem' => 'required',
            'nombre_rem' => 'required|min:3|max:20', 'apellido_rem' =>'min:3|max:20', 'tlf_rem' => 'required|regex:/^(\+?\d{10,11})$/',
            'correo_rem' => 'required', 'tipo_documento_dest' => 'required', 'documento_dest' => 'required', 'nombre_dest' => 'required',
            'apellido_dest' => 'required', 'direccion_dest' => 'required', 'codigo' => 'nullable|regex:/^[A-Z]{2}\d{9}VE$/',
            'tlf_dest' => 'required|regex:/^(\+?\d{10,11})$/', 'correo_dest' => 'required'];
            break;
            default:
                $reglas =  ['servicioss' =>'required', 'peso' => 'required', 'envio_seleccionado' =>'required', 'nombre_rem' =>'required|min:3|max:20',
            'apellido_rem' =>'required|min:3|max:20', 'tipo_documento_rem' =>'required', 'documento_rem' =>'required|integer',
            'codigo_postal_rem' =>'required', 'estadoss' =>'required', 'municipioss' =>'required', 'parroquiass' =>'required',
            'direccion_rem' =>'required|max:150', 'correo_rem' =>'required|email','tlf_rem' =>'required|regex:/^(\+?\d{10,11})$/',
            'nombre_dest' =>'required|min:3|max:25', 'apellido_dest' =>'required|min:3|max:25', 'tipo_documento_dest' =>'required',
            'documento_dest' =>'required|integer', 'codigo_postal_dest' =>'required', 'estadoss_dest_inter' =>'nullable',
            'municipioss_dest' =>'required', 'parroquiass_dest' =>'required', 'direccion_dest' =>'required|max:200',
            'tlf_dest' => 'required|regex:/^(\+?\d{10,11})$/', 'correo_dest' =>'required|email'];
            break;
        }


        switch ($this->servicioss) {
            case 1:
                $reglas = array_merge($reglas, ['peso' => 'required|numeric|min:0.01|max:2000']);
            break;
            case 9:
                $reglas = array_merge($reglas, ['peso' => 'required|numeric|min:0.01|max:30000']);
            break;
            case 14:
                $reglas = array_merge($reglas, ['peso' => 'required|numeric|min:0.01|max:2000']);
            break;
            case 15:
                $reglas = array_merge($reglas, ['peso' => 'required|numeric|min:0.01|max:2000']);
            break;
            case 17:
                $reglas = array_merge($reglas, ['peso' => 'required|numeric|min:0.01|max:1000']);
            break;
            case 18:
                $reglas = array_merge($reglas, ['peso' => 'required|numeric|min:5000|max:20000']);
            break;
            case 19:
                $reglas = array_merge($reglas, ['peso' => 'required|numeric|min:0.01|max:20000']);
            break;
            case 21:
                $reglas = array_merge($reglas, ['peso' => 'required|numeric|min:0.01|max:30000']);
            break;
        }
        return $reglas;
    }



    public function ObtenerInputs()
    {
        switch ($this->servicioss) {
            case '':
                return [];
            case 1:
            if ($this->corporativo && $this->recoleccion){
                return ['peso', 'contenido', 'subservicios', 'recoleccion', 'cliente_corporativo', 'tipo_documento_rem', 'documento',
                'nombre_rem', 'apellido_rem', 'estadoss', 'municipioss', 'parroquiass', 'ciudades_rem', 'codigo_postal_rem',
                'tlf_rem', 'correo_rem', 'direccion_rem', 'tipo_documento_dest', 'documento_dest', 'nombre_dest',
                'apellido_dest', 'estadoss_dest', 'municipioss_dest', 'ciudadess_dest', 'parroquiass_dest', 'codigo_postal_dest',
                'tlf_dest', 'correo_dest', 'direccion_dest'];

            }elseif($this->corporativo){
                return ['peso', 'contenido', 'subservicios', 'recoleccion', 'cliente_corporativo', 'tipo_documento_rem', 'documento',
                'nombre_rem', 'apellido_rem', 'tlf_rem', 'correo_rem', 'tipo_documento_dest', 'documento_dest', 'nombre_dest',
                'apellido_dest', 'estadoss_dest', 'municipioss_dest', 'ciudadess_dest', 'parroquiass_dest', 'codigo_postal_dest',
                'tlf_dest', 'correo_dest', 'direccion_dest'];
            }elseif($this->apartado){
                return ['peso', 'contenido', 'subservicios', 'apartado', 
                'tipo_documento_rem', 'documento_rem', 'nombre_rem', 'apellido_rem', 'ciudades_rem','tlf_rem', 'correo_rem',
                'estadoss_dest', 'opt', 'taquilla_postal', 'ciudadess_dest'];
            }else{
                return ['peso', 'contenido', 'subservicios', 'apartado', 'tipo_documento_rem', 'documento_rem', 'nombre_rem', 'apellido_rem',
                'tlf_rem', 'correo_rem', 'tipo_documento_dest', 'documento_dest',
                'nombre_dest', 'apellido_dest', 'estadoss_dest', 'municipioss_dest', 'ciudadess_dest', 'parroquiass_dest', 'codigo_postal_dest',
                'tlf_dest', 'correo_dest', 'direccion_dest'];
                }
            case 9:
            if ($this->corporativo && $this->recoleccion){
                return ['peso', 'contenido', 'insumos', 'recoleccion', 'cliente_corporativo', 'tipo_documento', 'documento',
                'nombre_rem', 'apellido_rem', 'estadoss', 'municipioss', 'parroquiass', 'ciudades_rem', 'codigo_postal_rem',
                'tlf_rem', 'correo_rem', 'direccion_rem', 'tipo_documento_dest', 'documento_dest', 'nombre_dest',
                'apellido_dest', 'estadoss_dest', 'municipioss_dest', 'ciudadess_dest', 'parroquiass_dest', 'codigo_postal_dest',
                'tlf_dest', 'correo_dest', 'direccion_dest'];
            }elseif($this->corporativo){
                return ['peso', 'contenido', 'insumos', 'recoleccion', 'cliente_corporativo', 'tipo_documento', 'documento',
                'nombre_rem', 'apellido_rem', 'tlf_rem', 'correo_rem', 'ciudades_rem',
                'tipo_documento_dest', 'documento_dest', 'nombre_dest',
                'apellido_dest', 'estadoss_dest', 'municipioss_dest', 'ciudadess_dest', 'parroquiass_dest', 'codigo_postal_dest',
                'tlf_dest', 'correo_dest', 'direccion_dest'];
            }elseif($this->apartado){
                return ['peso', 'contenido', 'insumos', 'apartado', 
                'tipo_documento_rem', 'documento_rem', 'nombre_rem', 'apellido_rem', 'ciudades_rem','tlf_rem', 'correo_rem',
                'estadoss_dest', 'opt', 'taquilla_postal', 'ciudadess_dest', ];
            }else{
                return ['peso', 'contenido', 'insumos', 'apartado', 'tipo_documento_rem', 'documento_rem', 'nombre_rem', 'apellido_rem',
                'ciudades_rem','tlf_rem', 'correo_rem', 'tipo_documento_dest', 'documento_dest',
                'nombre_dest', 'apellido_dest', 'estadoss_dest', 'municipioss_dest', 'ciudadess_dest', 'parroquiass_dest', 'codigo_postal_dest',
                'tlf_dest', 'correo_dest', 'direccion_dest'];
            }
            case 14:
            case 15:
            case 17:
            case 18:
            case 19:
            case 21:
            if($this->corporativo){
                return ['peso', 'contenido', 'subservicios_inter', 'codigo', 'cliente_corporativo', 'tipo_documento', 'documento',
                'nombre_rem', 'apellido_rem', 'tlf_rem', 'correo_rem', 'tipo_documento_dest', 'documento_dest', 'nombre_dest',
                'apellido_dest', 'continentess', 'paiss', 'estadoss_dest', 'municipioss_dest', 'ciudadess_dest', 'parroquiass_dest', 'codigo_postal_dest',
                'tlf_dest', 'correo_dest', 'direccion_dest'];
            }else{
                return ['peso', 'contenido', 'subservicios_inter', 'codigo', 'tipo_documento_rem', 'documento_rem', 'nombre_rem', 'apellido_rem',
                'tlf_rem', 'correo_rem', 'tipo_documento_dest','documento_dest', 'nombre_dest', 'apellido_dest', 'continentess',
                'paiss', 'direccion_dest', 'tlf_dest', 'correo_dest'];
            }
            default:
                return ['peso', 'contenido', 'subservicios', 'recoleccion', 'tipo_documento_rem', 'documento_rem', 'nombre_rem',
                'apellido_rem', 'tlf_rem', 'correo_rem', 'direccion_rem', 'tipo_documento_dest', 'documento_dest', 'nombre_dest',
                'apellido_dest',  'estadoss_dest', 'municipioss_dest', 'ciudadess_dest', 'parroquiass_dest', 'codigo_postal_dest',
                'tlf_dest', 'correo_dest', 'direccion_dest'];
        }
    }


    public function updatedClienteCorp()
    {
        if($this->cliente_corp){
            $cliente = ClienteCorporativo::where('cliente_corporativo_id', $this->cliente_corp)->first();
            $this->nombre_rem = $cliente->razon_social;
            $this->tipo_documento_rem = $cliente->tipo_documento;
            $this->documento_rem = $cliente->numero_documento;
            $this->apellido_rem = $cliente->agente_autorizado;
            $this->tlf_rem = $cliente->telefono;
            $this->correo_rem = $cliente->correo;
        }else{
            $cliente = '';
            $this->nombre_rem = '';
            $this->apellido_rem = '';
            $this->tlf_rem = '';
            $this->correo_rem = '';
            return;
        }
    }


    public function updatedTarifaEncon()
    {
        if (empty($this->tarifa_encon)) {
            $this->tarifa_selec = collect();
            $this->precio_total = 0;
            $this->iva = 0;
            $this->total_pagar = 0;
        } else {
            $this->tarifa_selec = TarifaNacionalConcepto::whereIn('tarifa_conceptos_id', $this->tarifa_encon)->get();
            $this->precio_total = $this->tarifa_selec->sum('monto');
            $this->iva = $this->precio_total * 0.16;
            $this->total_pagar = ceil($this->iva + $this->precio_total);
        }
    }

    public function updatedParametro()
    {
        $this->direccion_dest = '';
        foreach($this->parametro as $key => $direccion_dest){
            if($direccion_dest){
                if(strpos($this->direccion_dest, $key) === false){
                $this->direccion_dest .= $key . ': ' . $direccion_dest . ', ' ;
                }
            }
        }
    }


    public function updatedOptDest()
    {
        if($this->opt_dest){
            $this->apartados = CodigoApartadoPostal::where('oficina_id', $this->opt_dest)->where('operativo', true)->where('activo', true)->get();
        }else{
            $this->apartados = [];
        }
    }

    public function Apartado()
    {
        if($this->corporativo == true){
            $this->dispatch('alertSuccess2', message: 'Un cliente corporativo no puede acceder a esta opcion');
            $this->apartado = false;
        }
    }

    public function updatedCodigoApartadoPostal()
    {
        if($this->codigo_apartado_postal){
            $this->obtenerApartado();
        }else{
            return;
        }
    }

    public function obtenerApartado()
    {
        $this->aprobacion_apartado = '';

        if($this->codigo_apartado_postal){
            $this->taquilla_postal = $this->codigo_apartado_postal;
            $info_apartado = CodigoApartadoPostal::where('codigo_apartado_id', $this->codigo_apartado_postal)->where('oficina_id', $this->opt_dest)
            ->where('activo', true)
            ->where('operativo', true)->first();
            

            if($info_apartado){
                $registro_apartado = RegistroApartado::where('codigo_apartado_id', $info_apartado->codigo_apartado_id)
                ->where('activo', true)->latest()->first();
            $this->info_registro_apartado = $registro_apartado;

            $this->DatosApartado($this->info_registro_apartado);

            }else{
                $this->aprobacion_apartado = '';
                $this->dispatch('alertSuccess2', message: 'Error, No se encuentra un registro valido para este Apartado');
                return;
            }
        }else{
            $this->aprobacion_apartado = '';
            $this->dispatch('alertSuccess2', message: 'Error, No se encuentra un registro valido para este Apartado');
            return;
        }
    }


    private function DatosApartado($info_apartado)
    {
        $info = RegistroApartado::where('codigo_apartado_id', $info_apartado->codigo_apartado_id)->latest()->first();
        $oficina_apartado = Oficina::where('oficina_id', $info->oficina_id)->first();
        if(!empty($info))
        {
            $this->nombre_dest = $info->nombre;
            $this->apellido_dest = $info->apellido;
            $this->tipo_documento_dest = $info->tipo_documento;
            $this->documento_dest = $info->documento;
            $this->estadoss_dest = $oficina_apartado->estado_id;
            $this->municipioss_dest = $oficina_apartado->municipio_id;
            $this->parroquiass_dest = $oficina_apartado->parroquia_id;
            $this->direccion_dest = $oficina_apartado->direccion;
            $this->tlf_dest = $info->telefono;
            $this->correo_dest = $info->correo;
            $this->codigo_postal_dest = $info->codigo_postal;
            $this->oficina_dest_id = $info->oficina_id;

        }else{
            $this->dispatch('alertSuccess2', message: 'El numero de Apartado no esta registrado por ningun cliente');
            return;
        }
        $this->updatedMunicipiossDest($resest = false);
    }


    public function updatedInsumoSel()
    {
        if (empty($this->insumo_sel)){
            $this->insumo_selec = collect();
        }

        $this->insumo_selec = Insumo::whereIn('insumo_id', $this->insumo_sel)->get()->toArray();
        $suma = array_sum(array_column($this->insumo_selec, 'costo'));
        $pretotal = round($suma, 2);
        $this->insumo_pretotal = $pretotal;
        $this->updatedPeso();
    }


    public function updatedSubservicioEncon()
    {
        if (empty($this->subservicio_encon)){
            $this->subser = collect();
        }

        $this->certificado = in_array(14, $this->subservicio_encon) ? true : false;
        $this->subser = TarifaNacionalConcepto::whereIn('tarifa_conceptos_id', $this->subservicio_encon)->get()->toArray();
        $suma = array_sum(array_column($this->subser, 'monto'));
        $pretotal = round($suma, 2);
        $this->subser_pretotal = $pretotal;
        $this->subser_iva = round($pretotal * 0.16, 2);
        $this->total_subser = round($this->subser_iva + $pretotal, 2);
        $this->updatedPeso();
    }

    public function updatedSubservicioInterEncon()
    {
        if (empty($this->subservicio_inter_encon)){
            $this->subser_inter = collect();
        }
        $this->subser_inter = TarifaInternacionalConcepto::whereIn('tarifa_conceptos_internacional_id', $this->subservicio_inter_encon)->get()->toArray();
        $suma = array_sum(array_column($this->subser_inter, 'monto'));
        $this->total_subser_inter = round($suma, 2);
        $this->updatedPeso();
    }


    public function updatedServicioss($value)
    {
        $this->updatedRecoleccion();
        $this->corporativo = false;
        $this->recoleccion = false;
        $this->TF = $value;
        $this->tarifa_encon = [];
        $this->subservicio_encon = [];
        $this->subser = [];
        $this->subservicio_inter_encon = [];
        $this->subser_inter = [];
        $this->insumo_sel = [];

        if (empty($value)) {
            $this->tarifas = [];
            return;
        }

        switch ($value) {
            case 1:
            case 9:
                $this->mostrar_subservicio = true;
                $this->updatedPeso();
                break;
            case 14:
                $this->subservicios_inter = TarifaInternacionalConcepto::where('servicios_id', 16)
                ->whereIn('tarifa_conceptos_internacional_id', [1])->get();
                $this->mostrar_subservicio_inter = true;
                $this->mostrar_subservicio = false;
                $this->updatedPeso();
                break;
            case 15:
                $this->subservicios_inter = TarifaInternacionalConcepto::where('servicios_id', 16)
                ->whereIn('tarifa_conceptos_internacional_id', [1,6])->get();
                $this->mostrar_subservicio_inter = true;
                $this->mostrar_subservicio = false;
                $this->updatedPeso();
                break;
            case 17:
                $this->subservicios_inter = TarifaInternacionalConcepto::where('servicios_id', 16)
                ->whereIn('tarifa_conceptos_internacional_id', [1,6])->get();
                $this->mostrar_subservicio_inter = true;
                $this->mostrar_subservicio = false;
                $this->updatedPeso();
                break;
            case 18:
                $this->subservicios_inter = TarifaInternacionalConcepto::where('servicios_id', 16)
                ->whereIn('tarifa_conceptos_internacional_id', [1])->get();
                $this->mostrar_subservicio_inter = true;
                $this->mostrar_subservicio = false;
                $this->updatedPeso();
                break;
            case 19:
            case 21:
                $this->subservicios_inter = TarifaInternacionalConcepto::where('servicios_id', 16)
                ->whereIn('tarifa_conceptos_internacional_id', [1,6])->get();
                $this->mostrar_subservicio_inter = true;
                $this->mostrar_subservicio = false;
                $this->updatedPeso();
                break;
            case 12:
                break;
            case 16:
                $this->tarifas = $this->obtenerTarifasPorServicioInter(16);
                break;

            default:
            $this->updatedPeso();
                break;
        }
    }

    private function obtenerTarifasPorServicio($servicioId)
    {
        return TarifaNacionalConcepto::where('servicios_id', $servicioId)->get();
    }

    private function obtenerTarifasPorServicioInter($servicioId)
    {
        return TarifaInternacionalConcepto::where('servicios_id', $servicioId)->get();
    }


    public function actualizarTarifa($tarifaId)
    {
        if($this->envio_seleccionado === 'nacional'){
            $tarifa = TarifaNacionalConcepto::find($tarifaId);
        }else{
            $tarifa = TarifaInternacionalConcepto::find($tarifaId);
        }

        if ($tarifa && $tarifa->exclusion) {
                if (in_array($tarifa->exclusion, $this->tarifa_encon)) {
                    $this->tarifa_encon = array_diff($this->tarifa_encon, [$tarifa->exclusion]);
            }
        }
    }


    public function updatedRecoleccion()
    {
        $this->ObtenerInputs();

        $this->recoleccion_coste = '';
        if($this->peso <=0){
            $this->recol_mensj = 'Debe indicar el peso';
            $this->recoleccion = false;
            return;
        }elseif($this->servicioss == 9 && $this->tipo_envio_expreso !== 'Urbano'){
            $this->recol_mensj = 'El tipo de envio expreso debe ser urbano';
            $this->recoleccion = false;
        }else{
            $this->recol_mensj = '';
            $peso_convertido = $this->peso / 1000;
        }

        if($this->recoleccion){

            $this->estadoss = '';
            $this->municipioss = '';
            $this->parroquiass = '';
            $this->direccion_rem = '';
            $this->codigo_postal_rem = '';

            $recoleccion = TarifaNacionalRango::where('servicios_id', 8)
            ->where('desde', '<=', $peso_convertido)
            ->where('hasta', '>=', $peso_convertido)
            ->pluck('monto')->first();

            $this->iva_recolec = round($recoleccion * 0.16);
            $this->recoleccion_coste = round($recoleccion + $this->iva_recolec, 2);

            $this->recoleccion_monto = $recoleccion;
        }else{
            $info_oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
            $this->estadoss = $info_oficina->estado_id;
            $this->municipioss = $info_oficina->municipio_id;
            $this->parroquiass = $info_oficina->parroquia_id;
            $this->direccion_rem = $info_oficina->direccion;
            $this->codigo_postal_rem = $info_oficina->codigo_ubicacion;
            $this->recoleccion_monto = 0;
        }
        $this->updatedPeso();
    }


    public function updatedPeso()
    {
        $this->precio_total = 0;

        $this->peso = preg_replace('/[^\d]/', '', $this->peso);

        if($this->peso < 0.01){
            $this->iva = 0;
            $this->total_pagar = 0;
            return;

        }elseif($this->envio_seleccionado === 'nacional'){
            if($this->servicioss == 2 && $this->peso >= 500){
                $tarifa = TarifaInternacionalConcepto::where('tarifa_conceptos_internacional_id', 8)->first();
                }

            elseif($this->servicioss == 9){

                $tarifa = TarifaExpresoBolivariano::where('tipo_expreso', $this->tipo_envio_expreso)
                ->where('desde', '<=', $this->peso)
                ->where('hasta', '>=', $this->peso)
                ->first();

            }else{
                if($this->TF == ''){
                    return;
                }else{
                    $tarifa = TarifaNacionalRango::where('servicios_id', $this->TF)
                    ->where('desde', '<=', $this->peso)
                    ->where('hasta', '>=', $this->peso)
                    ->first();
                }
            }

        }elseif($this->envio_seleccionado === 'internacional'){

                $tarifa = TarifaInternacionalRango::where('servicios_id', $this->TF)
                ->where('desde', '<=', $this->peso)
                ->where('hasta', '>=', $this->peso)
                ->where('grupo', $this->continente_grupo)
                ->first();
        }

        $this->precio_total = $tarifa->monto ?? 0;
        $this->iva = ceil((($this->precio_total + $this->recoleccion_monto + $this->subser_pretotal + $this->insumo_pretotal) * 0.16) * 100) / 100;
        if($this->envio_seleccionado === 'nacional'){
                $this->total_pagar = ceil(($this->precio_total + $this->iva + $this->subser_pretotal + $this->recoleccion_monto + $this->insumo_pretotal) * 100) / 100;
        }else{
            $this->total_pagar = ceil(($this->precio_total + $this->iva + $this->total_subser_inter) * 100) / 100;
        }

    }


    public function TipoEnvioExpreso()
    {
        if($this->ciudades_dest == '' || $this->estadoss_dest == '' || $this->ciudades_rem == '' || $this->ciudadess_dest == ''){
            $this->tipo_envio_expreso = '';
            return;

        }else{
            if($this->ciudades_rem == $this->ciudadess_dest){
                $this->tipo_envio_expreso = 'Urbano';

            }elseif ($this->estadoss == $this->estadoss_dest){
                $this->tipo_envio_expreso = 'Intraestatal';
                $this->recoleccion = false;
            }else{
                $this->tipo_envio_expreso = 'Nacional';
                $this->recoleccion = false;
            }
        }
        $this->seb = $this->tipo_envio_expreso;
        $this->updatedPeso();

        if($this->tipo_envio_expreso){

            $this->estado_or = Estado::find($this->estadoss);
            $this->estado_dest = Estado::find($this->estadoss_dest);
            $this->ciudad_or = Ciudad::find($this->ciudades_rem);
            $this->ciudad_dest = Ciudad::find($this->ciudadess_dest);
        }else{
            return;
        }
    }


    public function updatedEnvioExpresoB()
    {
        $this->tipo_envio_expreso = $this->envio_expreso_b;
        if($this->peso == ''){
            return;
        }else{
            $tarifa = TarifaExpresoBolivariano::where('tipo_expreso', $this->envio_expreso_b)
                ->where('desde', '<=', $this->peso)
                ->where('hasta', '>=', $this->peso)
                ->first();
        }
        $this->seb = $this->envio_expreso_b;
        $this->updatedPeso();
    }


    public function agregar_pago()
    {
        // Validar los campos
        $this->validate([
            'metodo_pago_seleccionado' => 'required',
            'monto' => 'required|regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/',
        ]);

        // Convertir el monto a formato numérico (quitar formato de texto)
        $monto_numerico = floatval(str_replace(',', '.', str_replace('.', '', $this->monto)));

        if($monto_numerico > 10000000){
            $this->dispatch('alertSuccess2', message: 'El monto agregado es demasiado alto');
            return;
        }

        $tipo_pago = TipoPago::find($this->metodo_pago_seleccionado);
        if ($tipo_pago) {
            // Busca si el método de pago ya está en la matriz
            $pagoExistenteKey = array_search($tipo_pago->tipo_pago_id, array_column($this->pagos, 'tipo_pago_id'));
            if ($pagoExistenteKey !== false) {
                // Si existe, suma el nuevo monto al existente
                $this->pagos[$pagoExistenteKey]['monto'] += $monto_numerico;

            } else {
                // Si no existe, agrega un nuevo pago
                $this->pagos[] = [
                    'tipo_pago_id' => $tipo_pago->tipo_pago_id,
                    'nombre' => $tipo_pago->nombre,
                    'monto' => $monto_numerico,
                ];
            }
            $this->monto_pagado += $monto_numerico;
        }

        // Resetear los campos
        $this->metodo_pago_seleccionado = null;
        $this->monto = "";
    }


    public function eliminar_pago($tipo_pago_id)
    {
        // Verifica que haya un pago seleccionado
        if ($tipo_pago_id) {
            // Encuentra el índice del pago seleccionado
            $pagoKey = array_search($tipo_pago_id, array_column($this->pagos, 'tipo_pago_id'));

            // Si se encuentra el pago, elimínalo
            if ($pagoKey !== false) {
                $this->monto_pagado -= $this->pagos[$pagoKey]['monto'];
                unset($this->pagos[$pagoKey]);
                // Reindexa el array para evitar huecos
                $this->pagos = array_values($this->pagos);
            }
        }
    }


    public function updatedEnvioSeleccionado()
    {
        $this->servicioss = '';
        $this->subservicio_encon = [];
        $this->filtrarServicio();
    }


    public function filtrarServicio()
    {
        if ($this->envio_seleccionado === 'nacional') {
            // Filtra los servicios para nacionales
            $this->servicios_filtrados = array_filter($this->servicios, function($servicio) {
                return $servicio['nacional'] == true && !in_array($servicio['servicio_id'], [2,3,4,5,6,7,8,10,11,12,13]); // Ajustar segun sea necesario
            });
        } elseif ($this->envio_seleccionado === 'internacional') {
            // Filtra los servicios para internacionales
            $this->servicios_filtrados = array_filter($this->servicios, function($servicio) {
                return $servicio['nacional'] == false && !in_array($servicio['servicio_id'], [16,20,22]); // Ajustar segun sea necesario
            });
        } else {
            $this->servicios_filtrados = [];
        }
    }


    public function updatedContinentess()
    {
        if($this->continentess == ''){
            $this->paises = [];
            $this->continente_id = '';
            $this->continente_grupo = '';
        }else{

        $this->continente_id = $this->continentess;
        $this->continente_grupo = Continente::where('continente_id', $this->continentess)->pluck('grupo')->first();
        $this->updatedPeso();

            if($this->servicioss == 12){
                $this->updatedCantidadTarjetasPostales();
            }

        $this->paises = Pais::where('continente_id', $this->continente_id)->get();

        }
    }


    public function updatedPaiss($value)
    {
        if($value == ''){
            $this->estadoss_dest_inter = [];
        }else{
        $this->estadoss_dest_inter = Estado::where('pais_id', $value)->get();
        }
    }


    public function updatedEstadoss ()
    {
        if($this->estadoss == ''){
            $this->municipios = [];
        }else{
        $this->municipios = Municipio::where('estado_id', $this->estadoss)->get();

        if ($this->municipioss) {
            $this->municipioss = Municipio::where('estado_id', $this->estadoss)->pluck('municipio_id')->first();
            }
        }
        if($this->servicioss == 9){
            $this->TipoEnvioExpreso();
        }
    }


    public function updatedEstadossDest ($value)
    {
        if($this->apartado == true){
            $this->opt = Oficina::where('estado_id', $value)->where('tipo_oficina_id', 3)->where('estatus_id', 1)->get();
        }else{
            $this->opt = [];
        }

        // Al cambiar el estado se reinician sus campos dependientes del
        // destinatario: municipio, ciudad, parroquia y codigo postal (con sus
        // respectivas listas), para no arrastrar valores del estado anterior.
        $this->municipioss_dest = '';
        $this->ciudadess_dest = '';
        $this->parroquiass_dest = '';
        $this->codigo_postal_dest = '';
        $this->ciudades_dest = [];
        $this->parroquias_dest = [];
        $this->codigos_postales_dest = [];

        if($value == ''){
            $this->municipios_dest = [];
        }else{
        $this->municipios_dest = Municipio::where('estado_id', $value)->get();
        }

        $this->TipoEnvioExpreso();
    }


    public function updatedMunicipioss()
    {
        $this->ciudades_rem = '';

        if($this->municipioss == ''){
            $this->parroquias = [];
            $this->ciudades = [];
        }else{
        $this->parroquias = Parroquia::where('municipio_id', $this->municipioss)->get();
        $this->ciudades = Ciudad::where('municipio_id', $this->municipioss)->get();

        if ($this->parroquiass) {
            $this->parroquiass = Parroquia::where('municipio_id', $this->municipioss)->pluck('parroquia_id')->first();
            }
        }
    }


    public function updatedMunicipiossDest($reset = true)
    {
        if($reset){
            $this->parroquiass_dest = '';
        }
        $this->ciudades_dest = '';

        if($this->municipioss_dest == ''){
            $this->parroquias_dest = [];
            $this->ciudades_dest = [];
        }else{
        $this->parroquias_dest = Parroquia::where('municipio_id', $this->municipioss_dest)->get();
        $this->ciudades_dest = Ciudad::where('municipio_id', $this->municipioss_dest)->get();
        }
    }


    public function updatedParroquiass ()
    {
        if($this->parroquiass == ''){
            $this->codigos_postales_rem = [];
        }else{
            $this->codigos_postales_rem = Sector::where('parroquia_id', $this->parroquiass)->distinct()->pluck('codigo_postal')->sort();

            if ($this->codigo_postal_rem) {
                $this->codigo_postal_rem = Sector::where('codigo_postal', $this->codigo_postal_rem)->pluck('codigo_postal')->first();
                }
        }
    }


    public function updatedParroquiassDest ($value)
    {
        $this->codigo_postal_dest = '';

        if($value == ''){
            $this->codigos_postales_dest = [];
        }else{
            $this->codigos_postales_dest = Sector::where('parroquia_id', $value)->distinct()->pluck('codigo_postal')->sort();
        }
    }


    public function updatedCiudadesRem()
    {
        if($this->servicioss == 9){
            $this->TipoEnvioExpreso();
        }
    }


    public function updatedCiudadessDest()
    {
        if($this->servicioss == 9){
            $this->TipoEnvioExpreso();
        }
    }


    public function updatedTlfRem()
    {
        $this->tlf_rem= preg_replace('/[^\d]/', '', $this->tlf_rem);
    }

    public function updatedTlfDest()
    {
        $this->tlf_dest = preg_replace('/[^\d]/', '', $this->tlf_dest);
    }



    public function updatedDocumentoRem()
    {
        // Limpiar el número de documento para que solo contenga dígitos
        $this->documento_rem = preg_replace('/[^\d]/', '', $this->documento_rem);

        if (!empty($this->documento_rem)) {
            // Intentar obtener el cliente de la tabla Cliente
            $cliente = Cliente::where('numero_documento', $this->documento_rem)->first();

            if (!$cliente) {
               return;
            }

            if ($cliente) {
                $this->nombre_rem = $cliente->nombre;
                $this->apellido_rem = $cliente->apellido;
                $this->tipo_documento_rem = $cliente->tipo_documento;
                $this->tlf_rem = $cliente->telefono;
                $this->correo_rem = $cliente->correo;
                $this->cliente_existe = 1;
            } else {
                $this->cliente_existe = 0;
            }
        }
    }


    public function updatedDocumentoDest()
    {
        // Limpiar el número de documento para que solo contenga dígitos
        $this->documento_dest = preg_replace('/[^\d]/', '', $this->documento_dest);

        if (!empty($this->documento_dest)) {
            // Intentar obtener el cliente de la tabla Cliente
            $cliente = Cliente::where('numero_documento', $this->documento_dest)->first();

            if (!$cliente) {
                $cliente = ClienteCorporativo::where('numero_documento', $this->documento_dest)->first();
            }

            if ($cliente) {
                $this->setClienteDataDest($cliente);
                $this->destinatario_existe = 1;
            } else {
                $this->destinatario_existe = 0;
            }
        }
    }

    private function setClienteDataDest($cliente)
    {
        if ($cliente instanceof Cliente) {
            // Asignar datos de cliente de la tabla Cliente
            $this->nombre_dest = $cliente->nombre;
            $this->apellido_dest = $cliente->apellido;
            $this->tipo_documento_dest = $cliente->tipo_documento;
            $this->tlf_dest = $cliente->telefono;
            $this->correo_dest = $cliente->correo;

        } elseif ($cliente instanceof ClienteCorporativo) {
            // Asignar datos de cliente de la tabla ClienteCorporativo
            $this->nombre_dest = $cliente->razon_social;
            $this->apellido_dest = $cliente->agente_autorizado;
            $this->tipo_documento_dest = $cliente->tipo_documento;
            $this->tlf_dest = $cliente->telefono;
            $this->correo_dest = $cliente->correo;
        }
    }



    private function crearCodigoEnvioNacional()
    {
        $info_apartado = [];
        if ($this->usuario['oficina_id']) {

            $office = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
            $cod_origen = $office->codigo;

            if($this->servicioss == 2){
                if(empty($this->info_registro_apartado)){
                    $this->dispatch('alertSuccess2', message: 'Error, verifique que el apartado postal sea correcto');
                    return;
                }else{
                    $office_estado = Oficina::where('oficina_id', $this->info_registro_apartado['oficina_id'])->pluck('estado_id')->first();
                    $office_dest = Oficina::where('estado_id', $office_estado)->where('tipo_oficina_id', 4)->first();
                }
            }else{

                if($this->estadoss_dest == 2 || $this->estadoss_dest == 24){
                $office_dest = Oficina::where('estado_id', 1)->where('tipo_oficina_id', 4)->first();
                }else{
                $office_dest = Oficina::where('estado_id', $this->estadoss_dest)
                                    ->where('tipo_oficina_id', 4)
                                    ->first();
                }
            }

            if (!$office_dest) {
                $nuevo_correlativo = $this->generarCorrelativoSimplificado();
                $this->codigo_envio_creado = $cod_origen . $nuevo_correlativo . 'SO';
                return; // Manejar el caso donde no se encuentra la oficina destino
            }

            $cod_destino = $office_dest->codigo;

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


    public function submit()
    {
        $this->tipo_saca = TipoSaca::where('servicio_id', $this->servicioss)
        ->where('certificado', $this->certificado)->pluck('tipo_saca_id')->first();

        if($this->envio_seleccionado === 'nacional'){
            if($this->servicioss == 1 && $this->certificado == false){
                $estatus_envio = 2;
            }else{
                $estatus_envio = 1;
            }
        }else{
            $estatus_envio = 2;
        }
            

        DB::beginTransaction();

        try {
            if($this->cliente_existe == 0){
            //Guardar cliente remitente
            Cliente::Create([
                'numero_documento' => $this->documento_rem,
                'nombre' => $this->nombre_rem,
                'apellido' => $this->apellido_rem,
                'tipo_documento' => $this->tipo_documento_rem,
                'telefono' => $this->tlf_rem,
                'correo' => $this->correo_rem,
            ]);
        }

        if ($this->destinatario_existe == 0) {
            // Guardar cliente destinatario
            Cliente::create([
                'numero_documento' => $this->documento_dest,
                'nombre' => $this->nombre_dest,
                'apellido' => $this->apellido_dest,
                'tipo_documento' => $this->tipo_documento_dest,
                'telefono' => $this->tlf_dest,
                'correo' => $this->correo_dest,
            ]);
        }

        if($this->corporativo){
            $cliente = ClienteCorporativo::where('cliente_corporativo_id', $this->cliente_corp)->first();
            $this->nombre_rem = $cliente->razon_social;
            $this->apellido_rem = $cliente->agente_autorizado;
            $this->tipo_documento_rem = $cliente->tipo_documento;
            $this->documento_rem = $cliente->numero_documento;
            $this->tlf_rem = $cliente->telefono;
        }

        $envio = Envio::create([
            'servicio_id' => $this->servicioss,
            'tipo_envio' => $this->envio_seleccionado,
            'oficina_id' => $this->usuario['oficina_id'],
            'usuario_id' => $this->usuario['id'],
            'nombre_rem' => $this->nombre_rem,
            'apellido_rem' => $this->apellido_rem,
            'tipo_documento_rem' => $this->tipo_documento_rem,
            'documento_rem' => $this->documento_rem,
            'codigo_postal_rem' => $this->codigo_postal_rem,
            'estado_rem' => $this->estadoss,
            'municipio_rem' => $this->municipioss,
            'parroquia_rem' => $this->parroquiass,
            'ciudad_rem' => $this->ciudades_rem ?? null,
            'direccion_rem' => $this->direccion_rem,
            'correo_rem' => $this->correo_rem,
            'telefono_rem' => $this->tlf_rem,
            'nombre_dest' => $this->nombre_dest,
            'apellido_dest' => $this->apellido_dest,
            'tipo_documento_dest' => $this->tipo_documento_dest,
            'documento_dest' => $this->documento_dest,
            'codigo_postal_dest' => $this->codigo_postal_dest,
            'continente_dest' => $this->continentess,
            'pais_dest' => $this->paiss,
            'estado_dest' => $this->estadoss_dest,
            'municipio_dest' => $this->municipioss_dest,
            'parroquia_dest' => $this->parroquiass_dest,
            'ciudad_dest' => $this->ciudadess_dest ?? null,
            'direccion_dest' => $this->direccion_dest,
            'tlf_dest' => $this->tlf_dest,
            'correo_dest' => $this->correo_dest,
            'servicio_expreso' => $this->seb,
            'peso' => $this->peso,
            'coste' => $this->monto_pagado,
            'contenido' => $this->contenido,
            'apartado_postal' => $this->info_registro_apartado['codigo_apartado_id'] ?? null,
            'codigo_envio' => $this->codigo_envio,
            'devolucion' => false,
            'descubierto' => false,
            'tipo_saca_id' => $this->tipo_saca,
        ]);

        EnvioEncaminamiento::create([
            'envio_id' => $envio->envio_id,
            'oficina_id' => $envio->oficina_id,
            'usuario_id' => $this->usuario['id'],
            'estatus_id'  => $estatus_envio,
            'devolucion' => false,

        ]);

        EnvioAlmacen::create([
            'oficina_id' => $this->usuario['oficina_id'],
            'envio_id' => $envio->envio_id,
            'codigo' => $envio->codigo_envio,
            'saca_id' => null,
            'estatus' => true,
            'Entrada' => now()->toDateTimeString(),
        ]);


        
            $facturacion = Facturacion::create([
                'oficina_id' => $this->usuario['oficina_id'],
                'nombre' => $envio->nombre_rem,
                'apellido' => $envio->apellido_rem,
                'tipo_documento' => $envio->tipo_documento_rem,
                'documento' => $envio->documento_rem,
                'direccion' => $envio->direccion_rem,
                'monto_total' => $envio->coste,
                'iva' => $this->iva,
            ]);

            foreach($this->pagos as $pago){
                FacturacionPago::create([
                    'facturacion_id' => $facturacion->facturacion_id,
                    'tipo_pago_id' => $pago['tipo_pago_id'],
                    'monto' => $pago['monto'],
                ]);
            }

            FacturacionEnvio::create([
                'facturacion_id' => $facturacion->facturacion_id,
                'envio_id' => $envio->envio_id,
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'servicio_id' => $envio->servicio_id,
            ]);

            FacturacionDetalle::create([
                'servicio_id' => $envio->servicio_id,
                'facturacion_id' => $facturacion->facturacion_id,
                'monto' => $this->total_pagar
            ]);

            if($this->subservicio_encon ){
                foreach($this->subservicio_encon as $subserv_encon){
                    FacturacionTarifaEnvio::create([
                        'facturacion_id' => $facturacion->facturacion_id,
                        'envio_id' => $envio->envio_id,
                        'tipo_envio' => $envio->tipo_envio,
                        'tarifa_id' => $subserv_encon,
                    ]);
                }
            }elseif($this->subservicio_inter_encon){
                foreach($this->subservicio_inter_encon as $subserv_encon){
                    FacturacionTarifaEnvio::create([
                        'facturacion_id' => $facturacion->facturacion_id,
                        'envio_id' => $envio->envio_id,
                        'tipo_envio' => $envio->tipo_envio,
                        'tarifa_id' => $subserv_encon,
                    ]);
                }
            }
        


            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se creó el envío ({$envio->envio_id}) con código ({$envio->codigo_envio})",
            ]);

            DB::commit(); // Si todo sale bien, confirmamos la transacción
            $this->dispatch('alertSuccess', message: 'Envio Creado exitosamente!');
            if($this->envio_seleccionado === 'nacional'){
                $this->generarTermica($envio);
            }
            else{
                return redirect()->route('envios.listado-ventas');
            }

        } catch (\Exception $e) {
            //  dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en la creacion del envio, verifique los datos e intente de nuevo');
             // Si ocurre un error, revertimos todos los cambios
        }
    }

        private function generarTermica($envio)
        {
            if (in_array($this->servicioss, [1, 9])) {
                // Verificamos si el servicio es 1 y el certificado es true
                if ($this->servicioss == 1 && $this->certificado == true) {
                    return redirect()->route('generar-termica', ['envio' => $envio]);
                }
                // Si el servicio es 1 pero certificado es false, redirige a listado-ventas
                if ($this->servicioss == 1 && $this->certificado == false) {
                    return redirect()->route('envios.listado-ventas');
                }
                // Si el servicio es 9, genera termica sin importar certificado
                return redirect()->route('generar-termica', ['envio' => $envio]);
            } else {
                return redirect()->route('envios.listado-ventas');
            }
        }
    

        public function render()
        {
            return view('livewire.envios.envios-form');
        }
}
