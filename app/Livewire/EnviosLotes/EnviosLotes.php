<?php

namespace App\Livewire\EnviosLotes;

use App\Livewire\Concerns\CreaEnviosConCodigoUnico;
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
use App\Models\Parametro;
use App\Models\Parroquia;
use App\Models\Continente;
use App\Models\EnvioInsumo;
use App\Models\Facturacion;
use App\Models\EnvioAlmacen;
use App\Models\InsumoUsuario;
use App\Models\FacturacionPago;
use Livewire\Attributes\Layout;
use App\Models\FacturacionEnvio;

use App\Models\InventarioInsumo;
use App\Models\RegistroApartado;
use App\Models\ClienteCorporativo;
use App\Models\FacturacionDetalle;
use Illuminate\Support\Facades\DB;
use App\Models\ContratoCorporativo;
use App\Models\EnvioEncaminamiento;
use App\Models\TarifaNacionalRango;
use Illuminate\Support\Facades\Log;
use App\Models\CodigoApartadoPostal;
use App\Models\FacturacionTarifaEnvio;
use App\Models\TarifaNacionalConcepto;
use App\Models\TarifaExpresoBolivariano;
use App\Models\TarifaInternacionalRango;
use App\Models\TarifaInternacionalConcepto;
use App\Models\ClienteCorporativoAutorizado;
use App\Models\UsuarioSeguimiento;
use App\Rules\CodigosTelefono;

#[Layout('layouts.app')]
class EnviosLotes extends Component
{
    use CreaEnviosConCodigoUnico;

    public $nombre_rem, $apellido_rem, $tipo_documento_rem, $documento_rem, $codigo_postal_rem, $estadoss, $municipioss,
        $ciudades_rem, $parroquiass, $direccion_rem, $tlf_rem, $correo_rem, $showinter;

    public $nombre_dest, $apellido_dest, $tipo_documento_dest, $documento_dest, $codigo_postal_dest, $continentess, $paiss,
        $estadoss_dest, $estados_dest_inter, $municipioss_dest, $ciudadess_dest, $parroquiass_dest, $tlf_dest, $correo_dest,
        $doc_autorizado, $nombre_autorizado, $cliente_autorizado;

    public $mensaje, $servicioss, $seb, $taquilla_postal, $peso, $peso_internacional, $costo, $contenido, $tarifa,
        $cliente_existe, $destinatario_existe, $monto, $monto_pagado, $metodo_pago_seleccionado, $codigo_envio,
        $precio_total, $cantidad_tarjetas_postales, $continente_id, $continente_grupo, $cantidad_palabras, $telegrama,
        $tipo_envio_expreso, $envio_expreso_b, $codigo_envio_creado, $correlativo, $TF, $tipo_tarifa, $tarifa_selec, $total_subser,
        $recoleccion_coste, $recol_mensj, $codigo_apartado_postal, $correlativo_apartado, $aprobacion_apartado,
        $tipo_saca, $apartado, $oficina_dest_id, $codigo, $opt_dest, $total_lote, $peso_acumulado, $envios_acumulados;

    public $parametro = ['Avenida' => '', 'Calle' => '', 'Edificio' => '', 'Nro Casa/Depa' => ''];
    public $direccion_dest = '';
    public $certificado = false;
    public $mostrar_subservicio_inter = true;
    public $mostrar_subservicio = true;
    public $envio_seleccionado = 'nacional';
    public $documentos = [];
    public $clientes_corp = [];
    public $cliente_corp;
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
    public $servicio_tarifa = [];
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
    public $insumo_sel = [];
    public $insumo_selec = [];
    public $insumo_pretotal = 0;
    public $opt = [];
    public $apartados = [];
    public $id_envios = [];
    public $totales_por_servicio = [];
    public $contrato = [];
    public $info_oficina = [];
    public $cancelar_pago = false;
    public $recoleccion = false;
    public $aviso_envio = false;


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
        // Solo validamos en vivo las propiedades que tienen una regla activa
        // según el servicio y la modalidad actuales. Al cambiar de modalidad
        // (cuando aún no hay servicio) rules() está vacío y validateOnly
        // lanzaría "Missing [$rules/rules()]".
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }


    public function mount()
    {
        $this->usuario = auth()->user();
        $this->sacas = TipoSaca::all();
        $this->documentos = Documento::all();
        $this->continentes = Continente::all();
        $this->estados = Estado::all();
        $this->servicios = Servicio::where('es_envio', true)->get()->toArray();
        $this->metodos_pago = TipoPago::all();
        $this->subservicios = TarifaNacionalConcepto::where('servicios_id', 7)->whereIn('tarifa_conceptos_id', [14])->get();
        $this->clientes_corp = ClienteCorporativo::all();

        $this->info_oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->estadoss = $this->info_oficina['estado_id'];
        $this->municipioss = $this->info_oficina['municipio_id'];
        $this->parroquiass = $this->info_oficina['parroquia_id'];
        $this->direccion_rem = $this->info_oficina['direccion'];
        $this->codigo_postal_rem = $this->info_oficina['codigo_ubicacion'];

        if (session()->has('id_envios')) {
            $this->id_envios = session()->get('id_envios');
        }

        $this->filtrarServicio();
        $this->updatedMunicipioss($this->info_oficina['municipio_id']);
    }


    public function borrar()
    {
        $this->tarifa_encon = [];
        $this->subservicio_encon = [];
        $this->insumo_sel = [];
        $this->subservicio_inter_encon = [];
        $this->codigo = '';
        $this->recoleccion = false;
        $this->peso = '';
        $this->contenido = '';
        $this->nombre_rem = '';
        $this->apellido_rem = '';
        $this->tipo_documento_rem = '';
        $this->documento_rem = '';
        $this->cliente_corp = '';
        $this->estadoss = '';
        $this->municipioss = '';
        $this->parroquiass = '';
        $this->ciudades_rem = '';
        $this->codigo_postal_rem = '';
        $this->direccion_rem = '';
        $this->tlf_rem = '';
        $this->correo_rem = '';
        $this->nombre_dest = '';
        $this->apellido_dest = '';
        $this->tipo_documento_dest = '';
        $this->documento_dest = '';
        $this->continentess = '';
        $this->paiss = '';
        $this->estados_dest_inter = '';
        $this->estadoss_dest = '';
        $this->municipioss_dest = '';
        $this->parroquiass_dest = '';
        $this->ciudadess_dest = '';
        $this->codigo_postal_dest = '';
        $this->opt_dest = '';
        $this->codigo_apartado_postal = '';
        $this->parametro = ['Avenida' => '', 'Calle' => '', 'Edificio' => '', 'Nro Casa/Depa' => ''];
        $this->direccion_dest = '';
        $this->tlf_dest = '';
        $this->correo_dest = '';
        $this->metodo_pago_seleccionado = [];
        $this->monto = '';
        $this->pago_registrados = [];
        $this->monto_pagado = '';
        $this->pagos = [];
    }

    public function updatedCorporativo()
    {
        if ($this->corporativo == false) {
            $this->recoleccion = false;
        } else {
            $this->apartado = false;
        }
    }


    /**
     * Campos base del formulario según la modalidad (nacional / internacional).
     * Son idénticos para cualquier servicio de esa modalidad; los extras por
     * servicio se agregan encima en ObtenerInputs(). 'servicioss' siempre va.
     */
    private function camposBase(): array
    {
        $base = ['servicioss'];

        if ($this->envio_seleccionado === 'nacional') {
            return array_merge($base, [
                'peso',
                'contenido',
                'tipo_documento_rem',
                'documento_rem',
                'nombre_rem',
                'apellido_rem',
                'tlf_rem',
                'correo_rem',
                'tipo_documento_dest',
                'documento_dest',
                'nombre_dest',
                'apellido_dest',
                'estadoss_dest',
                'municipioss_dest',
                'parroquiass_dest',
                'ciudadess_dest',
                'codigo_postal_dest',
                'tlf_dest',
                'correo_dest',
                'direccion_dest',
            ]);
        }

        if ($this->envio_seleccionado === 'internacional') {
            return array_merge($base, [
                'codigo',
                'peso',
                'contenido',
                'tipo_documento_rem',
                'documento_rem',
                'nombre_rem',
                'apellido_rem',
                'tlf_rem',
                'correo_rem',
                'tipo_documento_dest',
                'documento_dest',
                'nombre_dest',
                'apellido_dest',
                'continentess',
                'paiss',
                'tlf_dest',
                'correo_dest',
                'direccion_dest',
            ]);
        }

        return $base;
    }

    /**
     * Campos extra aditivos según la combinación (servicio × modalidad).
     * Si un servicio nuevo necesita campos propios, se agregan aquí.
     */
    private function camposExtra(): array
    {
        $servicio = (int) $this->servicioss;
        $tipo = $this->envio_seleccionado;

        $extras = [
            'nacional' => [
                9 => ['insumos', 'ciudades_rem'],
            ],
            'internacional' => [
                14 => ['subservicios_inter'],
                15 => ['subservicios_inter'],
                16 => ['subservicios_inter'],
                17 => ['subservicios_inter'],
                18 => ['subservicios_inter'],
                19 => ['subservicios_inter'],
                21 => ['subservicios_inter'],
            ],
        ];

        return $extras[$tipo][$servicio] ?? [];
    }

    /**
     * Lista final de campos visibles = base(modalidad) + extras(servicio, modalidad).
     * Es la única fuente: la vista lo usa para mostrar y rules() para validar.
     */
    public function ObtenerInputs()
    {
        if (empty($this->servicioss) || empty($this->envio_seleccionado)) {
            return [];
        }

        return array_values(array_unique(
            array_merge($this->camposBase(), $this->camposExtra())
        ));
    }

    /**
     * Catálogo único de reglas por campo. Las reglas de validación se derivan
     * de aquí tomando solo los campos visibles, de modo que mostrar y validar
     * nunca se desincronizan.
     */
    private function catalogoReglas(): array
    {
        return [
            'servicioss'          => 'required',
            'peso'                => 'required|numeric|min:0.01',
            'contenido'           => 'required|max:300',
            'nombre_rem'          => 'required|min:3|max:20',
            'apellido_rem'        => 'min:3|max:20',
            'tipo_documento_rem'  => 'required',
            'documento_rem'       => 'required|min:6|max:12',
            'tlf_rem'             => ['required', new CodigosTelefono],
            'correo_rem'          => 'required',
            'nombre_dest'         => 'required',
            'apellido_dest'       => 'required',
            'tipo_documento_dest' => 'required',
            'documento_dest'      => 'required|min:6|max:12',
            'estadoss_dest'       => 'required',
            'municipioss_dest'    => 'required',
            'parroquiass_dest'    => 'required',
            'ciudadess_dest'      => 'required',
            'codigo_postal_dest'  => 'required',
            'continentess'        => 'required',
            'paiss'               => 'required',
            'direccion_dest'      => 'required|max:200',
            'tlf_dest'            => ['required', new CodigosTelefono],
            'correo_dest'         => 'required',
            'codigo'              => 'nullable|regex:/^[A-Z]{2}\d{9}VE$/',
        ];
    }

    /**
     * Override de la regla de 'peso' por servicio (cada servicio tiene su
     * propio rango de peso / tarifas).
     */
    private function reglaPesoPorServicio(): string
    {
        return match ((int) $this->servicioss) {
            1  => 'required|numeric|min:0.01|max:2000',
            9  => 'required|numeric|min:0.01|max:30000',
            14 => 'required|numeric|min:0.01|max:2000',
            15 => 'required|numeric|min:0.01|max:2000',
            16 => 'required|numeric|min:1|max:100',
            17 => 'required|numeric|min:0.01|max:1000',
            18 => 'required|numeric|min:1000|max:20000',
            19 => 'required|numeric|min:0.01|max:20000',
            21 => 'required|numeric|min:0.01|max:30000',
            default => 'required|numeric|min:0.01',
        };
    }

    public function rules()
    {
        $campos = $this->ObtenerInputs();
        $catalogo = $this->catalogoReglas();

        // Solo se validan los campos que se muestran al usuario.
        $reglas = array_intersect_key($catalogo, array_flip($campos));

        // Override del peso según el servicio seleccionado.
        if (in_array('peso', $campos, true)) {
            $reglas['peso'] = $this->reglaPesoPorServicio();
        }

        return $reglas;
    }



    public function updatedClienteCorp()
    {
        if ($this->cliente_corp) {
            $cliente = ClienteCorporativo::where('cliente_corporativo_id', $this->cliente_corp)->first();
            $this->nombre_rem = $cliente->razon_social;
            $this->tipo_documento_rem = $cliente->tipo_documento;
            $this->documento_rem = $cliente->numero_documento;
            $this->apellido_rem = $cliente->agente_autorizado;
            $this->tlf_rem = $cliente->telefono;
            $this->correo_rem = $cliente->correo;
        } else {
            $cliente = '';
            $this->tipo_documento_rem = '';
            $this->nombre_rem = '';
            $this->apellido_rem = '';
            $this->tlf_rem = '';
            $this->correo_rem = '';
            $this->doc_autorizado = '';
            $this->nombre_autorizado = '';
            $this->contrato = [];
            return;
        }
    }

    public function updatedDocAutorizado($doc)
    {
        if ($doc == '') {
            return;
        } else {
            $aut = ClienteCorporativoAutorizado::where('documento', $doc)->where('cliente_corporativo_id', $this->cliente_corp)
                ->where('activo', true)->first();
        }

        if ($aut) {
            $this->cliente_autorizado = $aut->cliente_corporativo_autorizado_id;
            $this->nombre_autorizado  = $aut->nombre;
        } else {
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

            if ($this->info_oficina['zona_economica_especial'] == false) {
                $this->iva = $this->precio_total * 0.16;
            } else {
                $this->iva = 0;
            }
            $this->total_pagar = bcdiv($this->iva + $this->precio_total, 1, 2);
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


    public function updatedOptDest()
    {
        if ($this->opt_dest) {
            $this->apartados = CodigoApartadoPostal::where('oficina_id', $this->opt_dest)->where('operativo', true)->where('activo', true)->get();
        } else {
            $this->apartados = [];
        }
    }

    public function Apartado()
    {
        if ($this->corporativo == true) {
            $this->dispatch('alertSuccess2', message: 'Un cliente corporativo no puede acceder a esta opcion');
            $this->apartado = false;
        }
    }

    public function updatedCodigoApartadoPostal()
    {
        if ($this->codigo_apartado_postal) {
            $this->obtenerApartado();
        } else {
            return;
        }
    }

    public function obtenerApartado()
    {
        $this->aprobacion_apartado = '';

        if ($this->codigo_apartado_postal) {
            $this->taquilla_postal = $this->codigo_apartado_postal;
            $info_apartado = CodigoApartadoPostal::where('codigo_apartado_id', $this->codigo_apartado_postal)->where('oficina_id', $this->opt_dest)
                ->where('activo', true)
                ->where('operativo', true)->first();


            if ($info_apartado) {
                $registro_apartado = RegistroApartado::where('codigo_apartado_id', $info_apartado->codigo_apartado_id)
                    ->where('activo', true)->latest()->first();
                $this->info_registro_apartado = $registro_apartado;

                $this->DatosApartado($this->info_registro_apartado);
            } else {
                $this->aprobacion_apartado = '';
                $this->dispatch('alertSuccess2', message: 'Error, No se encuentra un registro valido para este Apartado');
                return;
            }
        } else {
            $this->aprobacion_apartado = '';
            $this->dispatch('alertSuccess2', message: 'Error, No se encuentra un registro valido para este Apartado');
            return;
        }
    }


    private function DatosApartado($info_apartado)
    {
        $info = RegistroApartado::where('codigo_apartado_id', $info_apartado->codigo_apartado_id)->latest()->first();
        $oficina_apartado = Oficina::where('oficina_id', $info->oficina_id)->first();
        if (!empty($info)) {
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
        } else {
            $this->dispatch('alertSuccess2', message: 'El numero de Apartado no esta registrado por ningun cliente');
            return;
        }
        $this->updatedMunicipiossDest($resest = false);
    }


    public function updatedInsumoSel()
    {
        if (empty($this->insumo_sel)) {
            $this->insumo_selec = collect();
            $this->insumo_pretotal = 0;
            return;
        }

        $this->insumo_selec = Insumo::whereIn('insumo_id', $this->insumo_sel)->get()->toArray();

        $suma = array_sum(array_column($this->insumo_selec, 'costo'));
        $this->insumo_pretotal = bcdiv($suma, 1, 2);

        $this->updatedPeso();
    }

    /**
     * Unidades de cada insumo ya comprometidas por los envíos que están en el lote
     * (aún no facturados). Devuelve [insumo_id => cantidad_comprometida].
     */
    private function insumosComprometidosEnLote(): array
    {
        $comprometidos = [];

        foreach ($this->id_envios as $envio_data) {
            foreach ($envio_data['insumo_sel'] ?? [] as $insumo_id) {
                $comprometidos[$insumo_id] = ($comprometidos[$insumo_id] ?? 0) + 1;
            }
        }

        return $comprometidos;
    }

    /**
     * Verifica que haya stock para los insumos seleccionados, considerando lo que ya
     * está comprometido por otros envíos del lote. Devuelve los nombres de los insumos
     * sin stock suficiente (array vacío si todo está disponible).
     */
    private function insumosSinStock(): array
    {
        if (empty($this->insumo_sel)) {
            return [];
        }

        $comprometidos = $this->insumosComprometidosEnLote();
        $sinStock = [];

        $inventario = InsumoUsuario::where('oficina_id', $this->usuario['oficina_id'])
            ->where('usuario_id', $this->usuario['id'])
            ->whereIn('insumo_id', $this->insumo_sel)
            ->with('insumo')
            ->get()
            ->keyBy('insumo_id');

        foreach ($this->insumo_sel as $insumo_id) {
            $registro = $inventario->get($insumo_id);

            // Stock real menos lo ya comprometido por el lote; este envío necesita 1 unidad.
            $disponible = ($registro->cantidad ?? 0) - ($comprometidos[$insumo_id] ?? 0);

            if ($disponible < 1) {
                $sinStock[] = $registro?->insumo?->descripcion
                    ?? Insumo::where('insumo_id', $insumo_id)->value('descripcion')
                    ?? "Insumo #{$insumo_id}";
            }
        }

        return $sinStock;
    }




    public function updatedSubservicioEncon()
    {
        if (empty($this->subservicio_encon)) {
            $this->subser = collect();
        }

        $this->certificado = in_array(14, $this->subservicio_encon) ? true : false;
        $this->subser = TarifaNacionalConcepto::whereIn('tarifa_conceptos_id', $this->subservicio_encon)->get()->toArray();
        $suma = array_sum(array_column($this->subser, 'monto'));
        $pretotal = bcdiv($suma, 1, 2);
        $this->subser_pretotal = $pretotal;
        $this->subser_iva = bcdiv($pretotal * 0.16, 1, 2);
        $this->total_subser = bcdiv($this->subser_iva + $pretotal, 1, 2);
        $this->updatedPeso();
    }

    public function updatedSubservicioInterEncon()
    {
        if (empty($this->subservicio_inter_encon)) {
            $this->subser_inter = collect();
        }
        $this->subser_inter = TarifaInternacionalConcepto::whereIn('tarifa_conceptos_internacional_id', $this->subservicio_inter_encon)->get()->toArray();

        $this->certificado = collect($this->subser_inter)
            ->contains(fn($s) => strcasecmp($s['nombre'] ?? '', 'Certificado') === 0);

        $suma = array_sum(array_column($this->subser_inter, 'monto'));
        $this->total_subser_inter = bcdiv($suma, 1, 2);
        $this->updatedPeso();
    }

    /**
     * Concepto "Certificado" en cada catálogo de subservicios.
     */
    private const CONCEPTO_CERTIFICADO_INTER = 1;   // tarifas_internacionales_conceptos
    private const CONCEPTO_CERTIFICADO_NAC = 14;     // tarifas_nacionales_conceptos

    /**
     * Agrega automáticamente el subservicio "Certificado" según la modalidad:
     *   - Internacional -> concepto 1 en subservicio_inter_encon.
     *   - Nacional      -> concepto 14 en subservicio_encon.
     *
     * Solo el servicio 14 lleva certificado (en ambas modalidades). Ningún
     * otro servicio lo lleva.
     *
     * El usuario ya no lo marca a mano: se inyecta en la selección para que su
     * costo se sume al total, y el checkbox se oculta en la vista. En ambos
     * casos se reusa el mismo flujo que la selección manual.
     */
    private function aplicarCertificadoAutomatico()
    {
        // Solo el servicio 14 lleva certificado, en cualquier modalidad.
        if ((int) $this->servicioss !== 14) {
            return;
        }

        if ($this->envio_seleccionado === 'internacional') {
            if (!in_array(self::CONCEPTO_CERTIFICADO_INTER, $this->subservicio_inter_encon)) {
                $this->subservicio_inter_encon[] = self::CONCEPTO_CERTIFICADO_INTER;
            }

            // Recalcula subser_inter y total_subser_inter con el certificado.
            $this->updatedSubservicioInterEncon();
            return;
        }

        if ($this->envio_seleccionado === 'nacional') {
            if (!in_array(self::CONCEPTO_CERTIFICADO_NAC, $this->subservicio_encon)) {
                $this->subservicio_encon[] = self::CONCEPTO_CERTIFICADO_NAC;
            }

            // Recalcula subser, subser_pretotal y totales con el certificado.
            $this->updatedSubservicioEncon();
        }
    }


    public function updatedServicioss($value)
    {
        $this->corporativo = false;
        $this->recoleccion = false;
        $this->TF = $value;
        $this->tarifa_encon = [];
        $this->subservicio_encon = [];
        $this->subser = [];
        $this->subservicio_inter_encon = [];
        $this->subser_inter = [];
        $this->insumo_sel = [];
        $this->tipo_envio_expreso = null;

        if (empty($value)) {
            $this->tarifas = [];
            return;
        }

        if ($this->envio_seleccionado === 'nacional') {
            // Flujo nacional: subservicios nacionales + insumos.
            $this->mostrar_subservicio = true;
            $this->mostrar_subservicio_inter = false;
            $this->updatedInsumoSel();
            $this->updatedSubservicioEncon();
        } else {
            // Flujo internacional: subservicios internacionales según el servicio.
            $this->subservicios_inter = TarifaInternacionalConcepto::where('servicios_id', 16)
                ->whereIn('tarifa_conceptos_internacional_id', $this->conceptosInterPorServicio($value))
                ->get();
            $this->mostrar_subservicio_inter = true;
            $this->mostrar_subservicio = false;
        }

        // El certificado se inyecta automáticamente según modalidad y servicio
        // (el propio método decide si aplica y en qué array).
        $this->aplicarCertificadoAutomatico();

        $this->updatedPeso();
    }

    /**
     * Conceptos internacionales (modalidad de servicio) ofrecidos por servicio.
     * El 1 (Certificado) siempre está; algunos suman el 6 (Presentación Aduanal).
     */
    private function conceptosInterPorServicio($servicio): array
    {
        return match ((int) $servicio) {
            14, 18  => [1],
            default => [1, 6],
        };
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
        if ($this->envio_seleccionado === 'nacional') {
            $tarifa = TarifaNacionalConcepto::find($tarifaId);
        } else {
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
        if ($this->recoleccion == true && $this->peso <= 0) {
            $this->dispatch('alertSuccess3', message: 'Debe indicar el peso para acceder al servicio de recoleccion a domicilio');
            $this->recoleccion = false;
            return;
        } elseif ($this->servicioss == 9 && $this->tipo_envio_expreso !== 'Urbano') {
            $this->dispatch('alertSuccess3', message: 'EL tipo de Envio Expreso debe ser Urbano para acceder a el servicio de Recoleccion a Domicilio');
            $this->recoleccion = false;
        } else {
            $this->recol_mensj = '';
            $peso_convertido = $this->peso / 1000;
        }

        if ($this->recoleccion) {

            $this->estadoss = '';
            $this->municipioss = '';
            $this->parroquiass = '';
            $this->direccion_rem = '';
            $this->codigo_postal_rem = '';

            $recoleccion = TarifaNacionalRango::where('servicios_id', 8)
                ->where('desde', '<=', $peso_convertido)
                ->where('hasta', '>=', $peso_convertido)
                ->pluck('monto')->first();

            if ($this->info_oficina['zona_economica_especial'] == false) {
                $this->iva_recolec = bcdiv($recoleccion * 0.16, 1, 2);
            } else {
                $this->iva_recolec = 0;
            }
            $this->recoleccion_coste = bcdiv($recoleccion + $this->iva_recolec, 1, 2);

            $this->recoleccion_monto = $recoleccion;
        } else {
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


    /**
     * Configuración de la tarifa de peso por (servicio × modalidad).
     *
     * Devuelve:
     *   'fuente'       -> 'nacional' | 'internacional' | 'expreso_bol'
     *   'servicios_id' -> id a consultar en la tabla de tarifa (puede diferir
     *                     del servicio real: p.ej. el 14 nacional cobra como el 1)
     *
     * Un servicio puede tener tarifa distinta según la modalidad: el 14 usa su
     * tarifa internacional en internacional, pero reutiliza la del servicio 1
     * (nacional) cuando se envía como nacional.
     */
    private function configTarifaPeso(): array
    {
        $servicio = (int) $this->servicioss;
        $esNacional = $this->envio_seleccionado === 'nacional';

        // Servicios con tarifa propia en modalidad internacional.
        $internacionales = [14, 15, 16, 17, 18, 19, 21];

        // Servicio 9: expreso bolivariano (solo nacional).
        if ($servicio === 9) {
            return ['fuente' => 'expreso_bol', 'servicios_id' => 9];
        }

        // Servicios internacionales usados en modalidad internacional.
        if (in_array($servicio, $internacionales, true) && !$esNacional) {
            return ['fuente' => 'internacional', 'servicios_id' => $servicio];
        }

        // Servicios internacionales usados en modalidad NACIONAL reutilizan la
        // tarifa nacional del servicio 1 (no se cobra el monto internacional).
        if (in_array($servicio, $internacionales, true) && $esNacional) {
            return ['fuente' => 'nacional', 'servicios_id' => 1];
        }

        // Servicio 1 en modalidad INTERNACIONAL reutiliza la tarifa internacional
        // del servicio 14 (el 1 no tiene tarifa propia en internacional).
        if ($servicio === 1 && !$esNacional) {
            return ['fuente' => 'internacional', 'servicios_id' => 14];
        }

        // Servicio 1 nacional (y por defecto): tarifa nacional propia.
        return ['fuente' => 'nacional', 'servicios_id' => $servicio];
    }

    /**
     * Busca la tarifa de peso del servicio según su configuración (servicio ×
     * modalidad). El grupo de la tarifa internacional sale del continente
     * elegido por el usuario.
     */
    private function buscarTarifaPeso()
    {
        $config = $this->configTarifaPeso();

        switch ($config['fuente']) {
            case 'expreso_bol':
                return TarifaExpresoBolivariano::where('tipo_expreso', $this->tipo_envio_expreso)
                    ->where('desde', '<=', $this->peso)
                    ->where('hasta', '>=', $this->peso)
                    ->first();

            case 'internacional':
                return TarifaInternacionalRango::where('servicios_id', $config['servicios_id'])
                    ->where('desde', '<=', $this->peso)
                    ->where('hasta', '>=', $this->peso)
                    ->where('grupo', $this->continente_grupo)
                    ->first();

            case 'nacional':
            default:
                return TarifaNacionalRango::where('servicios_id', $config['servicios_id'])
                    ->where('desde', '<=', $this->peso)
                    ->where('hasta', '>=', $this->peso)
                    ->first();
        }
    }

    public function updatedPeso()
    {
        $this->precio_total = 0;

        $this->peso = preg_replace('/[^\d]/', '', $this->peso);

        if ($this->peso < 0.01) {
            $this->iva = 0;
            $this->total_pagar = 0;
            return;
        }

        $tarifa = $this->buscarTarifaPeso();

        $this->precio_total = $tarifa->monto ?? 0;

        // Base imponible: tarifa de peso + subservicios. En internacional los
        // subservicios viven en $total_subser_inter; en nacional, en las otras tres.
        if ($this->envio_seleccionado === 'internacional') {
            $base_iva = $this->precio_total + $this->total_subser_inter;
        } else {
            $base_iva = $this->precio_total + $this->recoleccion_monto + $this->subser_pretotal + $this->insumo_pretotal;
        }

        if ($this->info_oficina['zona_economica_especial'] == false) {
            $this->iva = bcdiv($base_iva * 0.16, 1, 2);
        } else {
            $this->iva = 0;
        }

        if ($this->envio_seleccionado === 'nacional') {
            $this->total_pagar = bcdiv($this->precio_total + $this->iva + $this->subser_pretotal + $this->recoleccion_monto + $this->insumo_pretotal, 1, 2);
        } else {
            $this->total_pagar = bcdiv($this->precio_total + $this->iva + $this->total_subser_inter, 1, 2);
        }
    }


    public function TipoEnvioExpreso()
    {
        if ($this->ciudades_dest == '' || $this->estadoss_dest == '' || $this->ciudades_rem == '' || $this->ciudadess_dest == '') {
            $this->tipo_envio_expreso = '';
            return;
        } else {
            if ($this->ciudades_rem == $this->ciudadess_dest) {
                $this->tipo_envio_expreso = 'Urbano';
            } elseif ($this->estadoss == $this->estadoss_dest) {
                $this->tipo_envio_expreso = 'Intraestatal';
                $this->recoleccion = false;
            } else {
                $this->tipo_envio_expreso = 'Nacional';
                $this->recoleccion = false;
            }
        }
        $this->seb = $this->tipo_envio_expreso;
        $this->updatedPeso();

        if ($this->tipo_envio_expreso) {

            $this->estado_or = Estado::find($this->estadoss);
            $this->estado_dest = Estado::find($this->estadoss_dest);
            $this->ciudad_or = Ciudad::find($this->ciudades_rem);
            $this->ciudad_dest = Ciudad::find($this->ciudadess_dest);
        } else {
            return;
        }
    }


    public function updatedTipoEnvioExpreso()
    {
        // $this->tipo_envio_expreso = $this->envio_expreso_b;
        if ($this->peso == '') {
            return;
        } else {
            $tarifa = TarifaExpresoBolivariano::where('tipo_expreso', $this->tipo_envio_expreso)
                ->where('desde', '<=', $this->peso)
                ->where('hasta', '>=', $this->peso)
                ->first();
        }
        $this->seb = $this->tipo_envio_expreso;
        $this->updatedPeso();
    }


    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
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
            // Solo se muestran estos servicios para envíos nacionales.
            $this->servicios_filtrados = array_filter($this->servicios, function ($servicio) {
                return in_array($servicio['servicio_id'], [1, 9, 14, 15]);
            });
        } elseif ($this->envio_seleccionado === 'internacional') {
            // Solo se muestran estos servicios para envíos internacionales.
            // (El servicio 23 / Bulto Postal se excluye: no tiene tarifa de peso configurada.)
            $this->servicios_filtrados = array_filter($this->servicios, function ($servicio) {
                return in_array($servicio['servicio_id'], [1, 14, 15, 16, 17, 18, 19, 23, 21]);
            });
        } else {
            $this->servicios_filtrados = [];
        }
    }


    public function updatedContinentess()
    {
        if ($this->continentess == '') {
            $this->paises = [];
            $this->continente_id = '';
            $this->continente_grupo = '';
        } else {

            $this->continente_id = $this->continentess;
            $this->continente_grupo = Continente::where('continente_id', $this->continentess)->pluck('grupo')->first();
            $this->updatedPeso();

            if ($this->servicioss == 12) {
                $this->updatedCantidadTarjetasPostales();
            }

            $this->paises = Pais::where('continente_id', $this->continente_id)->get();
        }
    }


    public function updatedPaiss($value)
    {
        if ($value == '') {
            $this->estadoss_dest_inter = [];
        } else {
            $this->estadoss_dest_inter = Estado::where('pais_id', $value)->get();
        }
    }


    public function updatedEstadoss()
    {
        if ($this->estadoss == '') {
            $this->municipios = [];
        } else {
            $this->municipios = Municipio::where('estado_id', $this->estadoss)->get();

            if ($this->municipioss) {
                $this->municipioss = Municipio::where('estado_id', $this->estadoss)->pluck('municipio_id')->first();
            }
        }
        if ($this->servicioss == 9) {
            $this->TipoEnvioExpreso();
        }
    }


    public function updatedEstadossDest($value)
    {
        if ($this->apartado == true) {
            $this->opt = Oficina::where('estado_id', $value)->where('tipo_oficina_id', 3)->where('estatus_id', 1)->get();
        } else {
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

        if ($value == '') {
            $this->municipios_dest = [];
        } else {
            $this->municipios_dest = Municipio::where('estado_id', $value)->get();
        }

        $this->TipoEnvioExpreso();
    }


    public function updatedMunicipioss()
    {
        $this->ciudades_rem = '';

        if ($this->municipioss == '') {
            $this->parroquias = [];
            $this->ciudades = [];
        } else {
            $this->parroquias = Parroquia::where('municipio_id', $this->municipioss)->get();
            $this->ciudades = Ciudad::where('municipio_id', $this->municipioss)->get();

            if ($this->parroquiass) {
                $this->parroquiass = Parroquia::where('municipio_id', $this->municipioss)->pluck('parroquia_id')->first();
            }
        }
    }


    public function updatedMunicipiossDest($reset = true)
    {
        if ($reset) {
            $this->parroquiass_dest = '';
        }
        $this->ciudades_dest = '';

        if ($this->municipioss_dest == '') {
            $this->parroquias_dest = [];
            $this->ciudades_dest = [];
        } else {
            $this->parroquias_dest = Parroquia::where('municipio_id', $this->municipioss_dest)->get();
            $this->ciudades_dest = Ciudad::where('municipio_id', $this->municipioss_dest)->get();
        }
    }


    public function updatedParroquiass()
    {
        if ($this->parroquiass == '') {
            $this->codigos_postales_rem = [];
        } else {
            $this->codigos_postales_rem = Sector::where('parroquia_id', $this->parroquiass)->distinct()->pluck('codigo_postal')->sort();

            if ($this->codigo_postal_rem) {
                $this->codigo_postal_rem = Sector::where('codigo_postal', $this->codigo_postal_rem)->pluck('codigo_postal')->first();
            }
        }
    }


    public function updatedParroquiassDest($value)
    {
        $this->codigo_postal_dest = '';

        if ($value == '') {
            $this->codigos_postales_dest = [];
        } else {
            $this->codigos_postales_dest = Sector::where('parroquia_id', $value)->distinct()->pluck('codigo_postal')->sort();
        }
    }


    public function updatedCiudadesRem()
    {
        if ($this->servicioss == 9) {
            $this->TipoEnvioExpreso();
        }
    }


    public function updatedCiudadessDest()
    {
        if ($this->servicioss == 9) {
            $this->TipoEnvioExpreso();
        }
    }


    public function updatedTlfRem()
    {
        $this->tlf_rem = preg_replace('/[^\d]/', '', $this->tlf_rem);
    }

    public function updatedTlfDest()
    {
        $this->tlf_dest = preg_replace('/[^\d]/', '', $this->tlf_dest);
    }



    public function updatedDocumentoRem()
    {
        // Limpiar el número de documento para que solo contenga dígitos
        $this->documento_rem = preg_replace('/[^\d]/', '', $this->documento_rem);

        // Validar que el número no exceda el rango permitido por BIGINT
        if (!empty($this->documento_rem) && strlen($this->documento_rem) > 18) {
            $this->cliente_existe = 0;
            return;
        }

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

        if (!empty($this->documento_dest) && strlen($this->documento_dest) > 18) {
            $this->destinatario_existe = 0;
            return;
        }

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

            if ($this->servicioss == 2) {
                if (empty($this->info_registro_apartado)) {
                    $this->dispatch('alertSuccess2', message: 'Error, verifique que el apartado postal sea correcto');
                    return;
                } else {
                    $office_estado = Oficina::where('oficina_id', $this->info_registro_apartado['oficina_id'])->pluck('estado_id')->first();
                    $office_dest = Oficina::where('estado_id', $office_estado)->where('tipo_oficina_id', 4)->where('externa', false)->first();
                }
            } else {

                if ($this->estadoss_dest == 2 || $this->estadoss_dest == 24) {
                    $office_dest = Oficina::where('estado_id', 1)->where('tipo_oficina_id', 4)->where('externa', false)->first();
                } elseif ($this->estadoss_dest == 20) {
                    $office_dest = Oficina::where('estado_id', 21)->where('tipo_oficina_id', 4)->where('externa', false)->first();
                } else {
                    $office_dest = Oficina::where('estado_id', $this->estadoss_dest)
                        ->where('tipo_oficina_id', 4)
                        ->where('externa', false)
                        ->first();
                }
            }

            if (!$office_dest) {
                $nuevo_correlativo = $this->generarCorrelativoSimplificado();
                $this->codigo_envio_creado = $cod_origen . $nuevo_correlativo . 'SO';
                return; // Manejar el caso donde no se encuentra la oficina destino
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


    //Maneja el aviso para ingresar otro envio
    public function decision($decision)
    {
        if ($decision === 'registrar') {
            $this->aviso_envio = false;
        } else {
            $this->aviso_envio = false;
            $this->realizar_pago();
        }
    }


    public function agregar_envio()
    {
        $this->validate();

        // Verificar stock de insumos antes de comprometerlos en el lote.
        // Se cuenta lo ya comprometido por los envíos que están en el lote sin facturar.
        $sinStock = $this->insumosSinStock();
        if (!empty($sinStock)) {
            $this->dispatch('alertSuccess2', message: 'Sin stock suficiente para: ' . implode(', ', $sinStock) . '. Verifique su inventario.');
            return;
        }

        if ($this->envio_seleccionado === 'nacional') {
            $this->paiss = null;
            $this->continentes = null;
            $this->continentess = null;
        } else {
            $this->estado_dest = '';
            $this->municipioss_dest = '';
            $this->parroquiass_dest = '';
            $this->ciudad_dest = '';
        }

        // Solo los servicios 1 (Correo Ordinario) y 14 (SPU Internacional) admiten variante
        // "no certificada". Para cualquier otro servicio se fuerza la variante certificada.
        $admiteNoCertificado = in_array((int) $this->servicioss, [1, 14], true);
        $filtroCertificado = $admiteNoCertificado ? (bool) $this->certificado : true;

        $this->tipo_saca = TipoSaca::where('servicio_id', $this->servicioss)
            ->where('certificado', $filtroCertificado)
            ->pluck('tipo_saca_id')
            ->first();

        if ($this->envio_seleccionado === 'nacional') {
            if ($this->servicioss == 1 && $this->certificado == false) {
                $estatus_envio = 2;
            } else {
                $estatus_envio = 1;
            }
        } else {
            $estatus_envio = 2;
        }

        // Si es corporativo, obtener datos del cliente corporativo
        $nombre_rem = $this->nombre_rem;
        $apellido_rem = $this->apellido_rem;
        $tipo_documento_rem = $this->tipo_documento_rem;
        $documento_rem = $this->documento_rem;
        $tlf_rem = $this->tlf_rem;

        if ($this->corporativo) {
            $cliente = ClienteCorporativo::where('cliente_corporativo_id', $this->cliente_corp)->first();
            $nombre_rem = $cliente->razon_social;
            $apellido_rem = $cliente->agente_autorizado;
            $tipo_documento_rem = $cliente->tipo_documento;
            $documento_rem = $cliente->numero_documento;
            $tlf_rem = $cliente->telefono;
        }

        // Acumular el envío en memoria sin persistir en BD
        $this->id_envios[] = [
            'servicio_id' => $this->servicioss,
            'tipo_envio' => $this->envio_seleccionado,
            'oficina_id' => $this->usuario['oficina_id'],
            'usuario_id' => $this->usuario['id'],
            'nombre_rem' => $nombre_rem,
            'apellido_rem' => $apellido_rem,
            'tipo_documento_rem' => $tipo_documento_rem,
            'documento_rem' => $documento_rem,
            'codigo_postal_rem' => $this->codigo_postal_rem,
            'estado_rem' => $this->estadoss,
            'municipio_rem' => $this->municipioss,
            'parroquia_rem' => $this->parroquiass,
            'ciudad_rem' => $this->ciudades_rem ?? null,
            'direccion_rem' => $this->direccion_rem,
            'correo_rem' => $this->correo_rem,
            'telefono_rem' => $tlf_rem,
            'nombre_dest' => $this->nombre_dest,
            'apellido_dest' => $this->apellido_dest,
            'tipo_documento_dest' => $this->tipo_documento_dest,
            'documento_dest' => $this->documento_dest,
            'codigo_postal_dest' => $this->codigo_postal_dest,
            'continente_dest' => $this->continentess,
            'pais_dest' => $this->paiss,
            'estado_dest' => $this->estadoss_dest,
            'municipio_dest' => $this->municipioss_dest !== '' ? $this->municipioss_dest : null,
            'parroquia_dest' => $this->parroquiass_dest !== '' ? $this->parroquiass_dest : null,
            'ciudad_dest' => $this->ciudadess_dest ?? null,
            'direccion_dest' => $this->direccion_dest,
            'oficina_dest_id' => $this->opt_dest ?? null,
            'tlf_dest' => $this->tlf_dest,
            'correo_dest' => $this->correo_dest,
            'servicio_expreso' => $this->seb,
            'peso' => $this->peso,
            'coste' => $this->total_pagar,
            'coste_sin_iva' => $this->precio_total,
            'tasa_bs' => Parametro::where('parametro_id', 1)->pluck('valor')->first(),
            'contenido' => $this->contenido,
            'apartado_postal' => $this->info_registro_apartado['codigo_apartado_id'] ?? null,
            'apartado_oficina_id' => $this->info_registro_apartado['oficina_id'] ?? null,
            'devolucion' => false,
            'descubierto' => false,
            'tipo_saca_id' => $this->tipo_saca,
            'contrato_corporativo_id' => $this->contrato['contrato_corporativo_id'] ?? null,
            'cliente_corporativo_autorizado_id' => $this->cliente_autorizado ?? null,
            'estatus_envio' => $estatus_envio,
            'iva' => $this->iva,
            'total_pagar' => $this->total_pagar,
            'certificado' => $this->certificado,
            // Datos para crear clientes si no existen
            'cliente_existe' => $this->cliente_existe,
            'destinatario_existe' => $this->destinatario_existe,
            'correo_rem_cliente' => $this->correo_rem,
            'tlf_rem_cliente' => $this->tlf_rem,
            'correo_dest_cliente' => $this->correo_dest,
            'tlf_dest_cliente' => $this->tlf_dest,
            // Datos para insumos
            'insumo_selec' => $this->insumo_selec,
            'insumo_sel' => $this->insumo_sel,
            // Datos para subservicios
            'subservicio_encon' => $this->subservicio_encon,
            'subservicio_inter_encon' => $this->subservicio_inter_encon,
        ];

        session()->put('id_envios', $this->id_envios);

        $this->insumo_sel = [];

        $this->dispatch('alertSuccess', message: 'Envio Agregado exitosamente!');

        $this->aviso_envio = true;
    }


    public function realizar_pago()
    {
        $this->cancelar_pago = true;

        // Reset variables to prevent duplication if called multiple times
        $this->total_lote = 0;
        $this->totales_por_servicio = [];

        foreach ($this->id_envios as $envio) {
            $this->total_lote += $envio['total_pagar'];

            // Si el servicio_id no existe en el array, inicializamos los valores
            if (!isset($this->totales_por_servicio[$envio['servicio_id']])) {
                $this->totales_por_servicio[$envio['servicio_id']] = [
                    'cant' => 0,      // Inicializamos la cantidad a 0
                    'total' => 0,     // Inicializamos el total a 0
                ];
            }

            // Incrementamos la cantidad y sumamos el total para el servicio_id correspondiente
            $this->totales_por_servicio[$envio['servicio_id']]['cant']++;
            $this->totales_por_servicio[$envio['servicio_id']]['total'] += $envio['total_pagar'];
        }
    }

    public function aprobar_pago()
    {
        // Se utiliza round para evitar errores por perdida de precision en punto flotante
        if (round((float) $this->monto_pagado, 2) >= round((float) $this->total_lote, 2)) {
            $this->generar_facturacion();
        } else {
            $this->dispatch('alertSuccess2', message: 'El monto pagado es menor al total a pagar');
            return;
        }
    }

    public function cerrar_pago()
    {
        $this->cancelar_pago = false;
        $this->total_lote = 0;
        $this->totales_por_servicio = [];
    }



    public function generar_facturacion()
    {
        $id_facturacion = [];
        $count_envios = count($this->id_envios);
        $count_pagos = count($this->pagos);
        $porcentajes_pagos = [];

        DB::beginTransaction();

        try {

            // === PASO 1: Persistir los envíos del lote ===
            $clientes_creados_rem = [];
            $clientes_creados_dest = [];

            foreach ($this->id_envios as $index => $envio_data) {

                // Crear cliente remitente si no existe (evitar duplicados dentro del mismo lote)
                if ($envio_data['cliente_existe'] == 0 && !in_array($envio_data['documento_rem'], $clientes_creados_rem)) {
                    Cliente::create([
                        'numero_documento' => $envio_data['documento_rem'],
                        'nombre' => $envio_data['nombre_rem'],
                        'apellido' => $envio_data['apellido_rem'],
                        'tipo_documento' => $envio_data['tipo_documento_rem'],
                        'telefono' => $envio_data['tlf_rem_cliente'],
                        'correo' => $envio_data['correo_rem_cliente'],
                    ]);
                    $clientes_creados_rem[] = $envio_data['documento_rem'];
                }

                // Crear cliente destinatario si no existe (evitar duplicados dentro del mismo lote)
                if ($envio_data['destinatario_existe'] == 0 && !in_array($envio_data['documento_dest'], $clientes_creados_dest)) {
                    Cliente::create([
                        'numero_documento' => $envio_data['documento_dest'],
                        'nombre' => $envio_data['nombre_dest'],
                        'apellido' => $envio_data['apellido_dest'],
                        'tipo_documento' => $envio_data['tipo_documento_dest'],
                        'telefono' => $envio_data['tlf_dest_cliente'],
                        'correo' => $envio_data['correo_dest_cliente'],
                    ]);
                    $clientes_creados_dest[] = $envio_data['documento_dest'];
                }

                // El código se genera al momento de persistir, dentro del reintento: un lote
                // concurrente puede tomar el mismo correlativo y hacer rebotar el insert
                // contra el índice único, y entonces hay que regenerarlo.
                $resolverCodigo = function () use ($envio_data) {
                    if ($envio_data['tipo_envio'] === 'nacional') {
                        $this->envio_seleccionado = 'nacional';
                        $this->servicioss = $envio_data['servicio_id'];
                        $this->estadoss_dest = $envio_data['estado_dest'];
                        $this->certificado = $envio_data['certificado'];
                        $this->info_registro_apartado = $envio_data['apartado_postal'] ? ['oficina_id' => $envio_data['apartado_oficina_id'], 'codigo_apartado_id' => $envio_data['apartado_postal']] : [];
                        $this->crearCodigoEnvioNacional();
                    } else {
                        $this->codigo_envio_creado = null;
                    }

                    return $this->codigo_envio_creado;
                };

                $envio = $this->crearEnvioConCodigoUnico($resolverCodigo, fn($codigoEnvio) => Envio::create([
                    'servicio_id' => $envio_data['servicio_id'],
                    'tipo_envio' => $envio_data['tipo_envio'],
                    'oficina_id' => $envio_data['oficina_id'],
                    'usuario_id' => $envio_data['usuario_id'],
                    'nombre_rem' => $envio_data['nombre_rem'],
                    'apellido_rem' => $envio_data['apellido_rem'],
                    'tipo_documento_rem' => $envio_data['tipo_documento_rem'],
                    'documento_rem' => $envio_data['documento_rem'],
                    'codigo_postal_rem' => $envio_data['codigo_postal_rem'],
                    'estado_rem' => $envio_data['estado_rem'],
                    'municipio_rem' => $envio_data['municipio_rem'],
                    'parroquia_rem' => $envio_data['parroquia_rem'],
                    'ciudad_rem' => $envio_data['ciudad_rem'],
                    'direccion_rem' => $envio_data['direccion_rem'],
                    'correo_rem' => $envio_data['correo_rem'],
                    'telefono_rem' => $envio_data['telefono_rem'],
                    'nombre_dest' => $envio_data['nombre_dest'],
                    'apellido_dest' => $envio_data['apellido_dest'],
                    'tipo_documento_dest' => $envio_data['tipo_documento_dest'],
                    'documento_dest' => $envio_data['documento_dest'],
                    'codigo_postal_dest' => $envio_data['codigo_postal_dest'],
                    'continente_dest' => $envio_data['continente_dest'],
                    'pais_dest' => $envio_data['pais_dest'],
                    'estado_dest' => $envio_data['estado_dest'],
                    'municipio_dest' => $envio_data['municipio_dest'],
                    'parroquia_dest' => $envio_data['parroquia_dest'],
                    'ciudad_dest' => $envio_data['ciudad_dest'],
                    'direccion_dest' => $envio_data['direccion_dest'],
                    'oficina_dest_id' => $envio_data['oficina_dest_id'],
                    'tlf_dest' => $envio_data['tlf_dest'],
                    'correo_dest' => $envio_data['correo_dest'],
                    'servicio_expreso' => $envio_data['servicio_expreso'],
                    'peso' => $envio_data['peso'],
                    'coste' => $envio_data['coste'],
                    'coste_sin_iva' => $envio_data['coste_sin_iva'],
                    'tasa_bs' => $envio_data['tasa_bs'] ?? null,
                    'contenido' => $envio_data['contenido'],
                    'apartado_postal' => $envio_data['apartado_postal'],
                    'codigo_envio' => $codigoEnvio,
                    'devolucion' => false,
                    'descubierto' => false,
                    'tipo_saca_id' => $envio_data['tipo_saca_id'],
                    'contrato_corporativo_id' => $envio_data['contrato_corporativo_id'],
                    'cliente_corporativo_autorizado_id' => $envio_data['cliente_corporativo_autorizado_id'],
                ]));

                // Crear insumos del envío
                if ($envio_data['insumo_selec'] && $envio_data['servicio_id'] == 9) {
                    foreach ($envio_data['insumo_selec'] as $insumo) {
                        EnvioInsumo::create([
                            'envio_id' => $envio->envio_id,
                            'insumo_id' => $insumo['insumo_id'],
                            'coste' => $insumo['costo']
                        ]);
                    }
                }

                // Crear encaminamiento
                EnvioEncaminamiento::create([
                    'envio_id' => $envio->envio_id,
                    'oficina_id' => $envio->oficina_id,
                    'usuario_id' => $envio_data['usuario_id'],
                    'estatus_id'  => $envio_data['estatus_envio'],
                    'devolucion' => false,
                ]);

                // Crear registro en almacén
                EnvioAlmacen::create([
                    'oficina_id' => $envio_data['oficina_id'],
                    'envio_id' => $envio->envio_id,
                    'codigo' => $envio->codigo_envio,
                    'saca_id' => null,
                    'estatus' => true,
                    'Entrada' => now()->toDateTimeString(),
                ]);

                // Crear tarifas de subservicios
                if ($envio_data['subservicio_encon']) {
                    foreach ($envio_data['subservicio_encon'] as $subserv_encon) {
                        FacturacionTarifaEnvio::create([
                            'envio_id' => $envio->envio_id,
                            'tipo_envio' => $envio->tipo_envio,
                            'tarifa_id' => $subserv_encon,
                        ]);
                    }
                } elseif ($envio_data['subservicio_inter_encon']) {
                    foreach ($envio_data['subservicio_inter_encon'] as $subserv_encon) {
                        FacturacionTarifaEnvio::create([
                            'envio_id' => $envio->envio_id,
                            'tipo_envio' => $envio->tipo_envio,
                            'tarifa_id' => $subserv_encon,
                        ]);
                    }
                }

                // Descontar insumos del inventario.
                // Si no hay stock se aborta el lote completo (rollback): no se puede
                // facturar un insumo que no existe en el inventario.
                if ($envio_data['servicio_id'] == 9 && $envio_data['insumo_selec']) {
                    foreach ($envio_data['insumo_sel'] as $insumo_id) {
                        $inventario_insumo = InsumoUsuario::where('oficina_id', $envio_data['oficina_id'])
                            ->where('usuario_id', $envio_data['usuario_id'])
                            ->where('insumo_id', $insumo_id)
                            ->lockForUpdate()
                            ->first();

                        if (!$inventario_insumo || $inventario_insumo->cantidad < 1) {
                            $descripcion = Insumo::where('insumo_id', $insumo_id)->value('descripcion') ?? "Insumo #{$insumo_id}";
                            throw new \RuntimeException("Sin stock del insumo '{$descripcion}'. El lote no se pudo facturar.");
                        }

                        $inventario_insumo->cantidad -= 1;
                        $inventario_insumo->save();
                    }
                }

                // Guardar el envio_id real para la facturación
                $this->id_envios[$index]['envio_id'] = $envio->envio_id;
            }

            // === PASO 2: Generar facturación ===
            foreach ($this->pagos as $pago) {
                if ($this->total_lote == 0) {
                    $porcentaje = 0;
                } else {
                    $porcentaje = $pago['monto'] / $this->total_lote;
                }
                $porcentajes_pagos[$pago['tipo_pago_id']] = $porcentaje;
            }

            foreach ($this->id_envios as $envio) {
                $facturacion = Facturacion::create([
                    'oficina_id' => $this->usuario['oficina_id'],
                    'nombre' => $envio['nombre_rem'],
                    'apellido' => $envio['apellido_rem'],
                    'tipo_documento' => $envio['tipo_documento_rem'],
                    'documento' => $envio['documento_rem'],
                    'direccion' => $envio['direccion_rem'],
                    'monto_total' => $envio['total_pagar'],
                    'iva' => $envio['iva'],
                ]);
                $id_facturacion[] = [
                    'facturacion_id' => $facturacion->facturacion_id,
                    'envio_id' => $envio['envio_id'],
                    'servicio_id' => $envio['servicio_id'],
                    'total_pagar' => $envio['total_pagar'],
                ];
            }

            foreach ($id_facturacion as $factura) {
                FacturacionDetalle::create([
                    'servicio_id' => $factura['servicio_id'],
                    'facturacion_id' => $factura['facturacion_id'],
                    'monto' => $factura['total_pagar'],
                ]);
            }


            foreach ($id_facturacion as $factura) {
                $factura_id = $factura['facturacion_id'];
                $total_envio = $factura['total_pagar'];

                $monto_pago = bcdiv($total_envio / $count_pagos, 1, 2);

                foreach ($this->pagos as $pago) {
                    $tipo_pago_id = $pago['tipo_pago_id'];
                    $porcentaje_pago = $porcentajes_pagos[$tipo_pago_id];

                    $monto_proporcion = bcdiv($total_envio * $porcentaje_pago, 1, 2);

                    FacturacionPago::create([
                        'facturacion_id' => $factura_id,
                        'tipo_pago_id' => $tipo_pago_id,
                        'monto' => $monto_proporcion,
                        'numero_referencia' => $pago['numero_referencia'],
                    ]);
                }
            }

            foreach ($id_facturacion as $factura) {
                FacturacionEnvio::create([
                    'facturacion_id' => $factura['facturacion_id'],
                    'envio_id' => $factura['envio_id'],
                    'oficina_id' => $this->usuario['oficina_id'],
                    'usuario_id' => $this->usuario['id'],
                    'servicio_id' => $factura['servicio_id'],
                ]);
            }

            $this->peso_acumulado = 0;

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró un lote de envíos con " . count($id_facturacion) . " envío(s) y total Bs " . $this->total_lote,
            ]);

            DB::commit();

            $this->dispatch('alertSuccess', message: 'Facturacion registrada correctamente');
            $this->dispatch('envio_registrado');
        } catch (\RuntimeException $e) {
            // Falta de stock de insumos: el lote no se factura y se informa el motivo real.
            DB::rollback();
            $this->dispatch('alertSuccess2', message: $e->getMessage());
            return;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al generar facturación del lote', [
                'usuario_id' => $this->usuario['id'] ?? null,
                'error'      => $e->getMessage(),
            ]);
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error, verifique que todos los campos esten llenados correctamente');
            return;
        }

        session()->forget('id_envios');
        $this->id_envios = [];
    }


    public function render()
    {
        $inventarios = InsumoUsuario::where('oficina_id', $this->usuario['oficina_id'])
            ->where('usuario_id', $this->usuario['id'])
            ->with('insumo')->orderBy('insumo_id')->get();

        // Stock disponible = cantidad en inventario menos lo ya comprometido por los
        // envíos que están en el lote sin facturar. El inventario en BD no se toca
        // hasta la facturación, por eso el descuento se refleja solo en la vista.
        $comprometidos = $this->insumosComprometidosEnLote();

        $inventarios->each(function ($inventario) use ($comprometidos) {
            $inventario->disponible = max(
                0,
                $inventario->cantidad - ($comprometidos[$inventario->insumo_id] ?? 0)
            );
        });

        return view('livewire.envios-lotes.envios-lotes', compact('inventarios'));
    }
}
