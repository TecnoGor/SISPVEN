<?php

namespace App\Livewire\Iposplus;

use App\Livewire\Concerns\CreaEnviosConCodigoUnico;
use App\Models\Envio;

use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Sector;
use App\Models\Cliente;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\TipoPago;
use App\Models\Documento;
use App\Models\Municipio;
use App\Models\Parametro;
use App\Models\Parroquia;
use App\Models\Facturacion;
use App\Models\EnvioAlmacen;
use App\Models\EnvioIposplus;
use App\Models\TarifaIposplus;
use App\Models\FacturacionPago;
use Livewire\Attributes\Layout;
use App\Models\FacturacionEnvio;
use App\Models\FacturacionDetalle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EnvioEncaminamiento;
use App\Models\UsuarioSeguimiento;
use App\Rules\CodigosTelefono;

#[Layout('layouts.app')]
class Iposplus extends Component
{
    use CreaEnviosConCodigoUnico;

    public $precio_total, $peso, $alto, $largo, $ancho, $estado, $municipio, $parroquia, $ciudad, $codigo_envio_creado,
        $contenido, $documento, $documento_dest, $nombre,
        $nombre_dest, $estado_dest, $municipio_dest, $ciudad_dest, $parroquia_dest, $codigo_postal, $codigo_postal_dest, $direccion,
        $direccion_dest, $telefono, $telefono_dest, $correo, $correo_dest, $monto_pagado, $tipo_documento, $tipo_documento_dest, $iva,
        $total_pagar, $monto, $apellido, $apellido_dest;

    public $estados = [];
    public $municipios_dest = [];
    public $parroquias_dest = [];
    public $ciudades_dest = [];
    public $codigos_postales_dest = [];
    public $documentos = [];
    public $documentos_dest = [];
    public $metodos_pago = [];
    public $pagos = [];
    public $usuario = [];
    public $oficina = [];
    public $cliente_existe = false;
    public $destinatario_existe = false;

    // Lote
    public $envios_lote = [];
    public $cantidad_envios = 0;
    public $subtotal_lote = 0;
    public $iva_lote = 0;
    public $total_lote = 0;
    public $modal_pago = false;

    // Modo de cálculo del peso: 'manual' o 'volumetrico'
    public $modo_peso = 'manual';
    public $peso_volumetrico = 0;          // valor calculado en kg
    public $excede_tarifa_max = false;     // bandera para advertencia
    public $kilo_max_tarifa = 0;           // máximo activo en TarifaIposplus

    protected function rules()
    {
        return [
            'peso' => 'required|numeric|min:0.001',
            'alto' => 'nullable|required_if:modo_peso,volumetrico|numeric|min:0.001|max:300',
            'ancho' => 'nullable|required_if:modo_peso,volumetrico|numeric|min:0.001|max:300',
            'largo' => 'nullable|required_if:modo_peso,volumetrico|numeric|min:0.001|max:300',
            'contenido' => 'required|max:300',
            'nombre' => 'required|max:20',
            'apellido' => 'required|max:20',
            'tipo_documento' => 'required',
            'documento' => 'required|digits_between:6,12',
            'telefono' => ['required', new CodigosTelefono],
            'correo' => 'required|email',
            'nombre_dest' => 'required|max:20',
            'apellido_dest' => 'required|max:20',
            'estado_dest' => 'required',
            'municipio_dest' => 'required',
            'parroquia_dest' => 'required',
            'ciudad_dest' => 'required',
            'codigo_postal_dest' => 'required',
            'tipo_documento_dest' => 'required',
            'documento_dest' => 'required|digits_between:6,12',
            'telefono_dest' => ['required', new CodigosTelefono],
            'correo_dest' => 'required|email|max:50',
            'direccion_dest' => 'required|max:200',
        ];
    }


    protected $messages = [
        'tipo_documento_dest.required' => 'El campo es obligatorio',
        'documento_dest.required' => 'El campo es obligatorio',
        'nombre_dest.required' => 'El campo es obligatorio',
        'estado_dest.required' => 'El campo es obligatorio',
        'ciudad_dest.required' => 'El campo es obligatorio',
        'parroquia_dest.required' => 'El campo es obligatorio',
        'codigo_postal_dest.required' => 'El campo es obligatorio',
        'direccion_dest.required' => 'El campo es obligatorio',
        'direccion_dest.max' => 'Maximo de 200 caracteres',
        'telefono_dest.required' => 'El campo es obligatorio',
        'correo_dest.required' => 'El campo es obligatorio',
        'correo_dest.email' => 'El formato del correo debe ser valido'
    ];



    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->estados = Estado::where('pais_id', 90)->get();
        $this->metodos_pago = TipoPago::all();
        $this->documentos = Documento::all();
        $this->documentos_dest = Documento::all();

        // Kilo máximo activo de las tarifas Iposplus
        $this->kilo_max_tarifa = (float) (TarifaIposplus::where('activo', true)->max('kilo_max') ?? 0);

        $this->obtenerOrigen();

        // Restaurar lote desde sesión si existe
        if (session()->has('envios_lote_iposplus')) {
            $this->envios_lote = session()->get('envios_lote_iposplus');
            $this->recalcularTotalesLote();
        }
    }


    public function updated($propertyName)
    {
        if ($propertyName !== 'peso') {
            $this->validateOnly($propertyName);
        }
    }

    public function limpiarFormulario()
    {
        $this->peso = '';
        $this->alto = '';
        $this->largo = '';
        $this->ancho = '';
        $this->contenido = '';
        $this->tipo_documento = '';
        $this->documento = '';
        $this->nombre = '';
        $this->apellido = '';
        $this->telefono = '';
        $this->correo = '';
        $this->tipo_documento_dest = '';
        $this->documento_dest = '';
        $this->nombre_dest = '';
        $this->apellido_dest = '';
        $this->estado_dest = '';
        $this->municipio_dest = '';
        $this->ciudad_dest = '';
        $this->parroquia_dest = '';
        $this->codigo_postal_dest = '';
        $this->direccion_dest = '';
        $this->telefono_dest = '';
        $this->correo_dest = '';
        $this->municipios_dest = [];
        $this->parroquias_dest = [];
        $this->ciudades_dest = [];
        $this->codigos_postales_dest = [];
        $this->precio_total = 0;
        $this->iva = 0;
        $this->total_pagar = 0;
        $this->cliente_existe = false;
        $this->destinatario_existe = false;
        $this->modo_peso = 'manual';
        $this->peso_volumetrico = 0;
        $this->excede_tarifa_max = false;
    }


    public function obtenerOrigen()
    {
        $this->estado = $this->oficina['estado_id'];
        $this->municipio = $this->oficina['municipio_id'];
        $this->parroquia = $this->oficina['parroquia_id'];
        $this->codigo_postal = $this->oficina['codigo_ubicacion'];
        $this->direccion = $this->oficina['direccion'];
    }


    public function updatedPeso()
    {
        $this->peso = str_replace(',', '.', $this->peso);
        $this->peso = preg_replace('/[^0-9.]/', '', $this->peso);

        // En modo volumétrico el peso se calcula automáticamente; ignorar entradas manuales
        if ($this->modo_peso === 'volumetrico') {
            return;
        }

        $this->CalcularPrecio();
    }

    public function updatedAlto()
    {
        $this->recalcularDesdeDimensiones();
    }

    public function updatedAncho()
    {
        $this->recalcularDesdeDimensiones();
    }

    public function updatedLargo()
    {
        $this->recalcularDesdeDimensiones();
    }

    public function updatedModoPeso()
    {
        if ($this->modo_peso === 'volumetrico') {
            $this->recalcularDesdeDimensiones();
        } else {
            $this->peso_volumetrico = 0;
            $this->CalcularPrecio();
        }
    }

    /**
     * Recalcula el peso volumétrico (cuando aplica) y dispara el cálculo de precio.
     */
    private function recalcularDesdeDimensiones()
    {
        if ($this->modo_peso !== 'volumetrico') {
            return;
        }

        $alto = (float) $this->alto;
        $ancho = (float) $this->ancho;
        $largo = (float) $this->largo;

        if ($alto > 0 && $ancho > 0 && $largo > 0) {
            // Truncar a 3 decimales sin redondear
            $this->peso_volumetrico = bcdiv((string) ($alto * $ancho * $largo), '5000', 3);
            $this->peso = $this->peso_volumetrico;
        } else {
            $this->peso_volumetrico = 0;
            $this->peso = '';
        }

        $this->CalcularPrecio();
    }

    private function CalcularPrecio()
    {
        $peso = (float) $this->peso;

        if ($peso <= 0) {
            $this->precio_total = 0;
            $this->iva = 0;
            $this->total_pagar = 0;
            $this->excede_tarifa_max = false;
            return;
        }

        // Si el peso supera la tarifa máxima activa, se toma la tarifa máxima como referencia
        $this->excede_tarifa_max = $this->kilo_max_tarifa > 0 && $peso > $this->kilo_max_tarifa;

        if ($this->excede_tarifa_max) {
            $tarifa = TarifaIposplus::where('activo', true)
                ->orderBy('kilo_max', 'desc')
                ->pluck('precio')
                ->first();
        } else {
            $tarifa = TarifaIposplus::where('activo', true)
                ->where('kilo_min', '<=', $peso)
                ->where('kilo_max', '>=', $peso)
                ->orderBy('tarifa_iposplus_id', 'asc')
                ->pluck('precio')
                ->first();
        }

        if (!$tarifa) {
            $this->precio_total = 0;
            $this->iva = 0;
            $this->total_pagar = 0;
            return;
        }

        $cambio = Parametro::where('parametro_id', 6)->pluck('valor')->first();
        $this->precio_total = bcdiv($tarifa * $cambio, 1, 2);

        $zonaEspecial = is_array($this->oficina)
            ? ($this->oficina['zona_economica_especial'] ?? false)
            : ($this->oficina->zona_economica_especial ?? false);

        if (!$zonaEspecial) {
            $this->iva = bcdiv($this->precio_total * 0.16, 1, 2);
        } else {
            $this->iva = 0;
        }

        $this->total_pagar = bcdiv($this->precio_total + $this->iva, 1, 2);
    }


    public function updatedDocumento()
    {
        if ($this->documento == '') {
            return;
        }

        $cliente = Cliente::where('numero_documento', $this->documento)->first();

        if ($cliente) {
            $this->cliente_existe = true;
            $this->nombre = $cliente->nombre;
            $this->apellido = $cliente->apellido;
            $this->tipo_documento = $cliente->tipo_documento;
            $this->telefono = $cliente->telefono;
            $this->correo = $cliente->correo;
        } else {
            $this->cliente_existe = false;
        }
    }

    public function updatedDocumentoDest()
    {
        if ($this->documento_dest == '') {
            return;
        }

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
        }
    }


    public function updatedEstadoDest()
    {
        // Al cambiar el estado se reinician sus campos dependientes del
        // destinatario: municipio, ciudad, parroquia y codigo postal (con sus
        // respectivas listas), para no arrastrar valores del estado anterior.
        $this->municipio_dest = '';
        $this->ciudad_dest = '';
        $this->parroquia_dest = '';
        $this->codigo_postal_dest = '';
        $this->ciudades_dest = [];
        $this->parroquias_dest = [];
        $this->codigos_postales_dest = [];

        if ($this->estado_dest == '') {
            $this->municipios_dest = [];
        } else {
            $this->municipios_dest = Municipio::where('estado_id', $this->estado_dest)->get();
        }
    }


    public function updatedMunicipioDest()
    {
        // Al cambiar el municipio se reinician ciudad, parroquia y codigo postal.
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

    public function updatedParroquiaDest()
    {
        // Al cambiar la parroquia se reinicia el codigo postal.
        $this->codigo_postal_dest = '';

        if ($this->parroquia_dest == '') {
            $this->codigos_postales_dest = [];
        } else {
            $this->codigos_postales_dest = Sector::where('parroquia_id', $this->parroquia_dest)->distinct()->pluck('codigo_postal')->sort();
        }
    }


    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }


    // ===========================================
    // LOTE
    // ===========================================

    public function agregarAlLote()
    {
        $this->validate();

        // Validación: suma de dimensiones no debe superar 300
        $sumaDim = (float) $this->alto + (float) $this->ancho + (float) $this->largo;
        if ($sumaDim > 300) {
            $this->dispatch('alertSuccess2', message: 'La suma de alto, ancho y largo no debe superar 300 cm.');
            return;
        }

        if (!$this->precio_total || $this->precio_total <= 0) {
            $this->dispatch('alertSuccess2', message: 'No se pudo calcular el precio del envío. Verifique el peso.');
            return;
        }

        // Tasa BCV Dólar vigente (Parametro id 1) al momento de agregar el envío al lote
        $tasaBs = Parametro::where('parametro_id', 1)->pluck('valor')->first();

        $this->envios_lote[] = [
            // Identificador único estable (no cambia al reordenar el array)
            'uid' => uniqid('lote_', true),

            // Remitente
            'tipo_documento_rem' => $this->tipo_documento,
            'documento_rem'      => $this->documento,
            'nombre_rem'         => $this->nombre,
            'apellido_rem'       => $this->apellido,
            'telefono_rem'       => $this->telefono,
            'correo_rem'         => $this->correo,
            'cliente_existe'     => $this->cliente_existe,

            // Origen (oficina del usuario)
            'estado_rem'        => $this->estado,
            'municipio_rem'     => $this->municipio,
            'parroquia_rem'     => $this->parroquia,
            'ciudad_rem'        => $this->ciudad ?? 'N/A',
            'codigo_postal_rem' => $this->codigo_postal,
            'direccion_rem'     => $this->direccion,

            // Destinatario
            'tipo_documento_dest' => $this->tipo_documento_dest,
            'documento_dest'      => $this->documento_dest,
            'nombre_dest'         => $this->nombre_dest,
            'apellido_dest'       => $this->apellido_dest,
            'telefono_dest'       => $this->telefono_dest,
            'correo_dest'         => $this->correo_dest,
            'destinatario_existe' => $this->destinatario_existe,

            // Destino
            'estado_dest'        => $this->estado_dest,
            'municipio_dest'     => $this->municipio_dest,
            'parroquia_dest'     => $this->parroquia_dest,
            'ciudad_dest'        => $this->ciudad_dest,
            'codigo_postal_dest' => $this->codigo_postal_dest,
            'direccion_dest'     => $this->direccion_dest,

            // Datos del envío
            'peso'      => $this->peso,
            'alto'      => $this->alto,
            'ancho'     => $this->ancho,
            'largo'     => $this->largo,
            'contenido' => $this->contenido,

            // Precios
            'precio_total' => (float) $this->precio_total,
            'iva'          => (float) $this->iva,
            'total_pagar'  => (float) $this->total_pagar,
            'tasa_bs'      => $tasaBs,
        ];

        $this->recalcularTotalesLote();
        session()->put('envios_lote_iposplus', $this->envios_lote);

        $this->dispatch('alertSuccess', message: 'Envío agregado al lote.');
    }

    public function eliminarDelLote($uid)
    {
        $indice = null;
        foreach ($this->envios_lote as $i => $envio) {
            if (($envio['uid'] ?? null) === $uid) {
                $indice = $i;
                break;
            }
        }

        if ($indice === null) {
            $this->dispatch('alertSuccess2', message: 'Envío no encontrado en el lote.');
            return;
        }

        unset($this->envios_lote[$indice]);
        $this->envios_lote = array_values($this->envios_lote);
        $this->recalcularTotalesLote();
        session()->put('envios_lote_iposplus', $this->envios_lote);

        $this->dispatch('alertSuccess', message: 'Envío eliminado del lote.');
    }

    private function recalcularTotalesLote()
    {
        $this->cantidad_envios = count($this->envios_lote);
        $this->subtotal_lote = 0;
        $this->iva_lote = 0;
        $this->total_lote = 0;

        foreach ($this->envios_lote as $envio) {
            $this->subtotal_lote = bcadd((string) $this->subtotal_lote, (string) $envio['precio_total'], 2);
            $this->iva_lote = bcadd((string) $this->iva_lote, (string) $envio['iva'], 2);
            $this->total_lote = bcadd((string) $this->total_lote, (string) $envio['total_pagar'], 2);
        }
    }

    public function abrirPago()
    {
        if ($this->cantidad_envios <= 0) {
            $this->dispatch('alertSuccess2', message: 'No hay envíos en el lote.');
            return;
        }

        $this->monto_pagado = 0;
        $this->pagos = [];
        $this->modal_pago = true;
    }

    public function cerrarPago()
    {
        $this->modal_pago = false;
        $this->monto_pagado = 0;
        $this->pagos = [];
    }


    // ===========================================
    // GENERACIÓN DE CÓDIGO (al pagar)
    // ===========================================

    private function generarCodigoEnvio($estadoDestId)
    {
        $office = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $cod_origen = $office->codigo;

        if ($estadoDestId == 2 || $estadoDestId == 24) {
            $office_dest = Oficina::where('estado_id', 1)->where('tipo_oficina_id', 4)->where('externa', false)->first();
        } elseif ($estadoDestId == 20) {
            $office_dest = Oficina::where('estado_id', 21)->where('tipo_oficina_id', 4)->where('externa', false)->first();
        } else {
            $office_dest = Oficina::where('estado_id', $estadoDestId)
                ->where('tipo_oficina_id', 4)
                ->where('externa', false)
                ->first();
        }

        if (!$office_dest) {
            // Sin oficina destino → correlativo simplificado
            $nuevo_correlativo = $this->generarCorrelativoSimplificado();
            return $cod_origen . $nuevo_correlativo . 'SO';
        }

        $cod_destino = $office_dest->codigo;

        if (!preg_match('/^(OP|CP|CO|CI|EX)\d{3}$/', $cod_destino)) {
            $estado_nombre = $office_dest->estado->nombre ?? 'Desconocido';
            throw new \Exception("La oficina '{$office_dest->nombre}' (ID: {$office_dest->oficina_id}) del estado {$estado_nombre} tiene un código inválido ({$cod_destino}).");
        }

        $envio_existente = Envio::where('codigo_envio', 'like', $cod_origen . $cod_destino . '%')
            ->orderBy('envio_id', 'desc')
            ->first();

        if ($envio_existente) {
            preg_match('/' . preg_quote($cod_origen) . preg_quote($cod_destino) . '(\d+)(?=\b)/', $envio_existente->codigo_envio, $matches);
            $ultimo_correlativo = isset($matches[1]) ? (int)$matches[1] : 0;
            $nuevo_correlativo = str_pad($ultimo_correlativo + 1, 9, '0', STR_PAD_LEFT);
        } else {
            $nuevo_correlativo = '000000001';
        }

        return $cod_origen . $cod_destino . $nuevo_correlativo;
    }

    private function generarCorrelativoSimplificado()
    {
        $envio_existente = Envio::orderBy('envio_id', 'desc')->first();

        if ($envio_existente) {
            preg_match('/\d{9}/', $envio_existente->codigo_envio, $matches);
            $ultimo_correlativo = isset($matches[0]) ? (int)$matches[0] : 0;
        } else {
            $ultimo_correlativo = 0;
        }

        return str_pad($ultimo_correlativo + 1, 9, '0', STR_PAD_LEFT);
    }


    // ===========================================
    // FINALIZAR LOTE (cobrar)
    // ===========================================

    public function finalizarLote()
    {
        if ($this->cantidad_envios <= 0) {
            $this->dispatch('alertSuccess2', message: 'No hay envíos en el lote.');
            return;
        }

        if (round((float) $this->monto_pagado, 2) < round((float) $this->total_lote, 2)) {
            $this->dispatch('alertSuccess2', message: 'El monto pagado no cubre el total del lote.');
            return;
        }

        if (!$this->usuario['oficina_id']) {
            $this->dispatch('alertSuccess2', message: 'El usuario no está asignado a ninguna oficina.');
            return;
        }

        try {
            DB::transaction(function () {
                // Cache de clientes ya creados en este lote para evitar duplicados
                $clientesCreados = [];
                $enviosCreados = [];

                foreach ($this->envios_lote as $datos) {
                    // 1. Cliente remitente (si no existe)
                    if (!$datos['cliente_existe'] && !in_array($datos['documento_rem'], $clientesCreados)) {
                        if (!Cliente::where('numero_documento', $datos['documento_rem'])->exists()) {
                            Cliente::create([
                                'numero_documento' => $datos['documento_rem'],
                                'nombre'           => $datos['nombre_rem'],
                                'apellido'         => $datos['apellido_rem'],
                                'tipo_documento'   => $datos['tipo_documento_rem'],
                                'telefono'         => $datos['telefono_rem'],
                                'correo'           => $datos['correo_rem'],
                            ]);
                            $clientesCreados[] = $datos['documento_rem'];
                        }
                    }

                    // 2. Cliente destinatario (si no existe)
                    if (!$datos['destinatario_existe'] && !in_array($datos['documento_dest'], $clientesCreados)) {
                        if (!Cliente::where('numero_documento', $datos['documento_dest'])->exists()) {
                            Cliente::create([
                                'numero_documento' => $datos['documento_dest'],
                                'nombre'           => $datos['nombre_dest'],
                                'apellido'         => $datos['apellido_dest'],
                                'tipo_documento'   => $datos['tipo_documento_dest'],
                                'telefono'         => $datos['telefono_dest'],
                                'correo'           => $datos['correo_dest'],
                            ]);
                            $clientesCreados[] = $datos['documento_dest'];
                        }
                    }

                    // 3 y 4. Generar código de envío y crear el envío. El código se resuelve
                    // dentro del reintento porque un lote concurrente puede tomar el mismo
                    // correlativo y hacer rebotar el insert contra el índice único.
                    $envio = $this->crearEnvioConCodigoUnico(
                        fn() => $this->generarCodigoEnvio($datos['estado_dest']),
                        fn($codigoEnvio) => Envio::create([
                        'servicio_id'         => 10,
                        'tipo_envio'          => 'nacional',
                        'oficina_id'          => $this->usuario['oficina_id'],
                        'usuario_id'          => $this->usuario['id'],
                        'nombre_rem'          => $datos['nombre_rem'],
                        'apellido_rem'        => $datos['apellido_rem'],
                        'tipo_documento_rem'  => $datos['tipo_documento_rem'],
                        'documento_rem'       => $datos['documento_rem'],
                        'codigo_postal_rem'   => $datos['codigo_postal_rem'],
                        'estado_rem'          => $datos['estado_rem'],
                        'municipio_rem'       => $datos['municipio_rem'],
                        'parroquia_rem'       => $datos['parroquia_rem'],
                        'ciudad_rem'          => $datos['ciudad_rem'],
                        'direccion_rem'       => $datos['direccion_rem'],
                        'correo_rem'          => $datos['correo_rem'],
                        'telefono_rem'        => $datos['telefono_rem'],
                        'nombre_dest'         => $datos['nombre_dest'],
                        'apellido_dest'       => $datos['apellido_dest'],
                        'tipo_documento_dest' => $datos['tipo_documento_dest'],
                        'documento_dest'      => $datos['documento_dest'],
                        'codigo_postal_dest'  => $datos['codigo_postal_dest'],
                        'continente_dest'     => null,
                        'pais_dest'           => null,
                        'estado_dest'         => $datos['estado_dest'],
                        'municipio_dest'      => $datos['municipio_dest'],
                        'parroquia_dest'      => $datos['parroquia_dest'],
                        'ciudad_dest'         => $datos['ciudad_dest'] ?? null,
                        'direccion_dest'      => $datos['direccion_dest'],
                        'tlf_dest'            => $datos['telefono_dest'],
                        'correo_dest'         => $datos['correo_dest'],
                        'servicio_expreso'    => null,
                        'peso'                => $datos['peso'] * 1000,
                        'coste'               => $datos['total_pagar'],
                        'contenido'           => $datos['contenido'],
                        'apartado_postal'     => null,
                        'codigo_envio'        => $codigoEnvio,
                        'devolucion'          => false,
                        'descubierto'         => false,
                        'tipo_saca_id'        => 14,
                        'coste_sin_iva'       => $datos['precio_total'],
                        'tasa_bs'             => $datos['tasa_bs'] ?? null,
                        ])
                    );

                    EnvioIposplus::create([
                        'envio_id' => $envio->envio_id,
                        'alto'     => $datos['alto'],
                        'largo'    => $datos['largo'],
                        'ancho'    => $datos['ancho'],
                    ]);

                    EnvioEncaminamiento::create([
                        'envio_id'   => $envio->envio_id,
                        'oficina_id' => $envio->oficina_id,
                        'usuario_id' => $this->usuario['id'],
                        'estatus_id' => 1,
                        'devolucion' => false,
                    ]);

                    EnvioAlmacen::create([
                        'oficina_id' => $this->usuario['oficina_id'],
                        'envio_id'   => $envio->envio_id,
                        'codigo'     => $envio->codigo_envio,
                        'saca_id'    => null,
                        'estatus'    => true,
                        'Entrada' => now()->toDateTimeString(),
                    ]);

                    $enviosCreados[] = $envio;
                }

                // 5. Calcular porcentajes de cada método de pago sobre el total del lote
                $porcentajes_pagos = [];
                foreach ($this->pagos as $pago) {
                    $porcentajes_pagos[$pago['tipo_pago_id']] = $this->total_lote > 0
                        ? $pago['monto'] / $this->total_lote
                        : 0;
                }

                // 6. Crear una Facturacion por cada envío
                $id_facturacion = [];
                foreach ($enviosCreados as $i => $envio) {
                    $datosEnvio = $this->envios_lote[$i] ?? null;
                    $totalEnvio = $datosEnvio['total_pagar'] ?? 0;
                    $ivaEnvio   = $datosEnvio['iva'] ?? 0;

                    $facturacion = Facturacion::create([
                        'oficina_id'     => $this->usuario['oficina_id'],
                        'nombre'         => $envio->nombre_rem,
                        'apellido'       => $envio->apellido_rem,
                        'tipo_documento' => $envio->tipo_documento_rem,
                        'documento'      => $envio->documento_rem,
                        'direccion'      => $envio->direccion_rem,
                        'monto_total'    => $totalEnvio,
                        'iva'            => $ivaEnvio,
                    ]);

                    $id_facturacion[] = [
                        'facturacion_id' => $facturacion->facturacion_id,
                        'envio_id'       => $envio->envio_id,
                        'servicio_id'    => $envio->servicio_id,
                        'total_pagar'    => $totalEnvio,
                    ];
                }

                // 7. FacturacionDetalle por cada factura
                foreach ($id_facturacion as $factura) {
                    FacturacionDetalle::create([
                        'servicio_id'    => $factura['servicio_id'],
                        'facturacion_id' => $factura['facturacion_id'],
                        'monto'          => $factura['total_pagar'],
                    ]);
                }

                // 8. FacturacionPago prorrateado por cada factura, por cada método
                foreach ($id_facturacion as $factura) {
                    foreach ($this->pagos as $pago) {
                        $porcentaje = $porcentajes_pagos[$pago['tipo_pago_id']] ?? 0;
                        $monto_proporcion = bcdiv((string)($factura['total_pagar'] * $porcentaje), '1', 2);

                        FacturacionPago::create([
                            'facturacion_id'    => $factura['facturacion_id'],
                            'tipo_pago_id'      => $pago['tipo_pago_id'],
                            'monto'             => $monto_proporcion,
                            'numero_referencia' => $pago['numero_referencia'] ?? null,
                        ]);
                    }
                }

                // 9. FacturacionEnvio (relación factura ↔ envío)
                foreach ($id_facturacion as $factura) {
                    FacturacionEnvio::create([
                        'facturacion_id' => $factura['facturacion_id'],
                        'envio_id'       => $factura['envio_id'],
                        'oficina_id'     => $this->usuario['oficina_id'],
                        'usuario_id'     => $this->usuario['id'],
                        'servicio_id'    => $factura['servicio_id'],
                    ]);
                }
            });

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se finalizó un lote Iposplus con {$this->cantidad_envios} envío(s) y total Bs {$this->total_lote}",
            ]);

            // Limpiar lote y sesión
            $this->envios_lote = [];
            $this->recalcularTotalesLote();
            session()->forget('envios_lote_iposplus');
            $this->cerrarPago();
            $this->limpiarFormulario();

            $this->dispatch('alertSuccess', message: 'Lote facturado y envíos creados exitosamente.');
        } catch (\Throwable $e) {
            Log::error('Error al finalizar lote Iposplus', [
                'usuario_id' => $this->usuario['id'] ?? null,
                'cantidad'   => $this->cantidad_envios,
                'error'      => $e->getMessage(),
            ]);
            $this->dispatch('alertSuccess2', message: 'Ocurrió un error al procesar el lote. Intente de nuevo.');
        }
    }


    public function render()
    {
        return view('livewire.iposplus.iposplus');
    }
}
