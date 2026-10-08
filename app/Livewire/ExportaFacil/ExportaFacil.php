<?php

namespace App\Livewire\ExportaFacil;
use App\Models\Pais;

use App\Models\Envio;
use App\Models\EnvioAlmacen;
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
use App\Models\Continente;
use App\Models\Facturacion;
use App\Models\FacturacionPago;
use Livewire\Attributes\Layout;
use App\Models\FacturacionEnvio;
use App\Models\EnvioExportaFacil;
use App\Models\ClienteCorporativo;
use App\Models\FacturacionDetalle;
use App\Models\TarifaExportaFacil;
use Illuminate\Support\Facades\DB;
use App\Models\EnvioEncaminamiento;
use App\Models\TarifaNacionalRango;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class ExportaFacil extends Component
{
    public $tipo_divisa, $precio_total, $peso, $continente, $pais_destino, $grupo, $estado, $municipio, $parroquia, $ciudad,
    $recoleccion, $cliente_corporativo, $agente, $contenido, $clase_correo, $codigo_rastreo, $documento, $nombre,
    $nombre_dest, $estado_dest, $ciudad_dest, $parroquia_dest, $codigo_postal, $codigo_postal_dest, $direccion, $direccion_dest,
    $telefono, $telefono_dest, $correo, $correo_dest, $monto_pagado, $tipo_documento, $iva, $total_pagar, $metodo_pago_seleccionado,
    $monto, $pago_registrados, $cliente_sel, $total_recolec, $apellido;

    public $paises = [];
    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $ciudades = [];
    public $codigos_postales = [];
    public $continentes = [];
    public $documentos = [];
    public $metodos_pago = [];
    public $pagos = [];
    public $usuario = [];
    public $oficina = [];
    public $corporativos = [];
    public $cliente_existe = false;

    const t_correo = [
        'EMS' => 'E',
        'Encomiendas' => 'C',
        'Cartas' => 'U',
    ];


    protected $rules = ['cliente_sel' => 'required_if:cliente_corporativo,true',
    'codigo_rastreo' => 'required|regex:/^[A-Z]{2}\d{9}VE$/',
    'pais_destino' => 'required', 'clase_correo' => 'required', 'peso' => 'required|numeric|min:0.1|max:100',
    'contenido' => 'required|max:300', 'nombre' => 'required|max:40', 'tipo_documento' => 'required',
    'documento' => 'required|digits_between:8,12', 'telefono' => 'required|regex:/^(\+?\d{10,11})$/', 'correo' => 'required|email',
    'estado' => 'required', 'municipio' => 'required', 'parroquia' => 'required', 'ciudad' => 'required_if:recoleccion,true',
    'codigo_postal' => 'required', 'direccion' => 'required|max:200', 'nombre_dest' => 'required|max:40',
    'telefono_dest' => 'required|digits:11', 'correo_dest' => 'required|email|max:50', 'estado_dest' => 'required|max:30',
    'parroquia_dest' => 'required|max:30', 'ciudad_dest' => 'required|max:30', 'codigo_postal_dest' => 'required|numeric',
    'telefono_dest' => 'required|regex:/^(\+?\d{10,11})$/', 'correo_dest' => 'required|email', 'direccion_dest' => 'required|max:200'];

    protected $messages = [
    'cliente_sel.required_if' => 'Debe seleccionar un cliente corporativo',
    'nombre_dest.required' => 'El campo es obligatorio', 'nombre_dest.regex' => 'Solo debe contener letras', 'nombre_dest.max' => 'Maximo de 40 caracteres',
    'estado_dest.required' => 'El campo es obligatorio', 'estado_dest.max' => 'Maximo de 30 caracteres',
    'ciudad_dest.required' => 'El campo es obligatorio', 'ciudad_dest.max' => 'Maximo de 30 caracteres',
    'parroquia_dest.required' => 'El campo es obligatorio', 'parroquia_dest.max' => 'Maximo de 30 caracteres',
    'codigo_postal_dest.required' => 'El campo es obligatorio', 'codigo_postal_dest.numeric' => 'Solo caracteres numericos',
    'direccion_dest.required' => 'El campo es obligatorio', 'direccion_dest.max' => 'Maximo de 200 caracteres',
    'telefono_dest.required' => 'El campo es obligatorio', 'teelfono_dest.regex' => 'Debe ser un formato de telefono valido',
    'correo_dest.required' => 'El campo es obligatorio', 'correo_dest.email' => 'El formato del correo debe ser valido'];


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }


    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->paises = Pais::where('exporta_facil', true)->get();
        $this->estados = Estado::where('pais_id', 90)->get();
        $this->metodos_pago = TipoPago::all();

        $this->obtenerDocumentos();
        $this->updatedRecoleccion();
    }

    public function obtenerDocumentos()
    {
        if($this->cliente_corporativo == true){
            $this->documentos = Documento::whereNotIn('documento_id', [2,4,6])->get();
        }else{
            $this->documentos = Documento::all();
        }
    }


    public function updatedClienteCorporativo()
    {
        $this->obtenerDocumentos();
        if($this->cliente_corporativo == false){
            $this->recoleccion = false;
        }else{
            $this->corporativos = ClienteCorporativo::all();
        }
    }

    public function updatedRecoleccion()
    {
        if($this->recoleccion){
            $this->estado = '';
            $this->municipio = '';
            $this->parroquia = '';
            $this->direccion = '';
            $this->codigo_postal = '';

            if($this->peso <= 0){
                return;
            }else{
                $peso_conv = $this->peso / 1000;
                $recoleccion = TarifaNacionalRango::where('servicios_id', 8)->where('desde', '<=', $peso_conv)
                                                    ->where('hasta', '>=', $peso_conv)
                                                    ->pluck('monto')->first();

                $iva = ($this->oficina && $this->oficina->zona_economica_especial == false)
                    ? bcdiv($recoleccion * 0.16, 1, 2)
                    : 0;
                $this->total_recolec = bcdiv($iva + $recoleccion, 1, 2);
                $this->calcularPrecio();
            }

        }else{
            $this->total_recolec = 0;

            $this->estado = $this->oficina['estado_id'];
            $this->municipio = $this->oficina['municipio_id'];
            $this->parroquia = $this->oficina['parroquia_id'];
            $this->codigo_postal = $this->oficina['codigo_ubicacion'];
            $this->direccion = $this->oficina['direccion'];

            $this->calcularPrecio();
        }
    }

    public function updatedClienteSel()
    {
        if($this->cliente_sel){
            $cliente_corp = ClienteCorporativo::where('cliente_corporativo_id', $this->cliente_sel)->first();
            $this->nombre = $cliente_corp->razon_social;
            $this->agente = $cliente_corp->agente_autorizado;
            $this->tipo_documento = $cliente_corp->tipo_documento;
            $this->documento = $cliente_corp->numero_documento;
            $this->telefono = $cliente_corp->telefono;
            $this->correo = $cliente_corp->correo;
        }
    }

    public function updatedDocumento()
    {
        if($this->documento == ''){
            return;
        }

        if($this->cliente_corporativo == false){
            $cliente = Cliente::where('numero_documento', $this->documento)->first();

            if ($cliente) {
                $this->cliente_existe = true;
                $this->nombre = $cliente->nombre;
                $this->apellido = $cliente->apellido;
                $this->tipo_documento = $cliente->tipo_documento;
                $this->telefono = $cliente->telefono;
                $this->correo = $cliente->correo;
            } else {
                return;
            }
        }else{
            $this->cliente_existe = false;
        }
    }

    public function updatedPaisDestino()
    {
        $this->updatedPeso();
    }


    public function updatedPeso()
    {
        $this->updatedRecoleccion();

        if($this->peso == 0 || $this->peso == ''){
            $this->precio_total = 0;
        }else{
            $this->precio_total = TarifaExportaFacil::where('pais_id', $this->pais_destino)
                                                    ->pluck('monto')
                                                    ->first();
        }
        $this->calcularPrecio();
    }

    private function calcularPrecio()
    {
        if($this->peso == ''){
            return;
        }else{
            $cambio = Parametro::where('parametro_id', 1)->pluck('valor')->first();
            $this->precio_total = $this->precio_total * $this->peso;
            $this->precio_total = bcdiv($this->precio_total * $cambio, 1, 2);
        }
        
        if ($this->oficina && $this->oficina->zona_economica_especial == false) {
            $this->iva = bcdiv($this->precio_total * 0.16, 1, 2);
        } else {
            $this->iva = 0;
        }

        $this->total_pagar = bcdiv($this->precio_total + $this->iva + $this->total_recolec, 1, 2);
    }



    public function updatedEstado()
    {
        if($this->estado == ''){
            $this->municipios = [];
        }else{
            $this->municipios = Municipio::where('estado_id', $this->estado)->get();
        }
    }

    public function updatedMunicipio()
    {
        if($this->municipio == ''){
            $this->parroquias = [];
            $this->ciudades = [];
        }else{
            $this->parroquias = Parroquia::where('municipio_id', $this->municipio)->get();
            $this->ciudades = Ciudad::where('municipio_id', $this->municipio)->get();
        }
    }

    public function updatedParroquia()
    {
        if($this->parroquia == ''){
            $this->codigos_postales = [];
        }else{
            $this->codigos_postales = Sector::where('parroquia_id', $this->parroquia)->distinct()->pluck('codigo_postal')->sort();
        }
    }


    // public function agregar_pago()
    // {
    //     // Validar los campos
    //     $this->validate([
    //         'metodo_pago_seleccionado' => 'required',
    //         'monto' => 'required|regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$/',
    //     ]);

    //     // Convertir el monto a formato numérico (quitar formato de texto)
    //     $monto_numerico = floatval(str_replace(',', '.', str_replace('.', '', $this->monto)));


    //     $tipo_pago = TipoPago::find($this->metodo_pago_seleccionado);
    //     if ($tipo_pago) {
    //         // Busca si el método de pago ya está en la matriz
    //         $pagoExistenteKey = array_search($tipo_pago->tipo_pago_id, array_column($this->pagos, 'tipo_pago_id'));
    //         if ($pagoExistenteKey !== false) {
    //             // Si existe, suma el nuevo monto al existente
    //             $this->pagos[$pagoExistenteKey]['monto'] += $monto_numerico;

    //         } else {
    //             // Si no existe, agrega un nuevo pago
    //             $this->pagos[] = [
    //                 'tipo_pago_id' => $tipo_pago->tipo_pago_id,
    //                 'nombre' => $tipo_pago->nombre,
    //                 'monto' => $monto_numerico,
    //             ];
    //         }
    //         $this->monto_pagado += $monto_numerico;
    //     }

    //     // Resetear los campos
    //     $this->metodo_pago_seleccionado = null;
    //     $this->monto = "";
    // }

    // public function eliminar_pago($tipo_pago_id)
    // {
    //     // Verifica que haya un pago seleccionado
    //     if ($tipo_pago_id) {
    //         // Encuentra el índice del pago seleccionado
    //         $pagoKey = array_search($tipo_pago_id, array_column($this->pagos, 'tipo_pago_id'));

    //         // Si se encuentra el pago, elimínalo
    //         if ($pagoKey !== false) {
    //             $this->monto_pagado -= $this->pagos[$pagoKey]['monto'];
    //             unset($this->pagos[$pagoKey]);
    //             // Reindexa el array para evitar huecos
    //             $this->pagos = array_values($this->pagos);
    //         }
    //     }
    // }
    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }



    public function info_rastreo()
    {
        $this->dispatch('alertSuccess3', message: 'El codigo debe llevar el siguiente formato S10: AB123456789VE');
    }

    public function habilitar()
    {
        if($this->monto_pagado > 0 && $this->monto_pagado >= $this->total_pagar){
            $this->validate();
            $this->submit();
        }else{
            $this->validate();
            $this->dispatch('alertSuccess2', message: 'Error, el monto pagado no coincide con el monto a pagar');
        }
    }


    public function submit()
    {
        DB::beginTransaction();

    try{
        if($this->cliente_existe == false){
            {
                Cliente::Create([
                    'numero_documento' => $this->documento,
                    'nombre' => $this->nombre,
                    'apellido' => $this->apellido,
                    'tipo_documento' => $this->tipo_documento,
                    'telefono' => $this->telefono,
                    'correo' => $this->correo,
                ]);
            }
        }

        $envio = Envio::create([
            'servicio_id' => 22,
            'tipo_envio' => 'internacional',
            'oficina_id' => $this->usuario['oficina_id'],
            'usuario_id' => $this->usuario['id'],
            'nombre_rem' => $this->nombre,
            'apellido_rem' => $this->apellido,
            'tipo_documento_rem' => $this->tipo_documento,
            'documento_rem' => $this->documento,
            'codigo_postal_rem' => $this->oficina['codigo_postal'],
            'estado_rem' => $this->oficina['estado_id'],
            'municipio_rem' => $this->oficina['municipio_id'],
            'parroquia_rem' => $this->oficina['parroquia_id'],
            'ciudad_rem' => $this->ciudades_rem ?? null,
            'direccion_rem' => $this->direccion,
            'correo_rem' => $this->correo,
            'telefono_rem' => $this->telefono,
            'nombre_dest' => $this->nombre_dest,
            'apellido_dest' => $this->apellido_dest ?? 'N/A',
            'tipo_documento_dest' => $this->tipo_documento_dest ?? null,
            'documento_dest' => $this->documento_dest ?? null,
            'codigo_postal_dest' => $this->codigo_postal_dest,
            'continente_dest' => $this->continentess ?? null,
            'pais_dest' => $this->pais_destino,
            'estado_dest' => null,
            'municipio_dest' => null,
            'parroquia_dest' => null,
            'ciudad_dest' => null,
            'direccion_dest' => $this->direccion_dest,
            'tlf_dest' => $this->telefono_dest,
            'correo_dest' => $this->correo_dest,
            'servicio_expreso' => null,
            'peso' => $this->peso * 1000,
            'coste' => $this->monto_pagado,
            'contenido' => $this->contenido,
            'apartado_postal' => null,
            'codigo_envio' => $this->codigo_rastreo,
            'devolucion' => false,
            'descubierto' => false,
            'tipo_saca_id' => 12,
            'tasa_bs' => Parametro::where('parametro_id', 1)->pluck('valor')->first(),
        ]);

        EnvioExportaFacil::create([
            'envio_id' => $envio->envio_id,
            'clase_correo' => $this->clase_correo,
            'estado_dest' => $this->estado_dest,
            'ciudad_dest' => $this->ciudad_dest,
            'parroquia_dest' => $this->parroquia_dest,
            'codigo_postal_dest' => $this->codigo_postal_dest,
        ]);

        EnvioEncaminamiento::create([
            'envio_id' => $envio->envio_id,
            'oficina_id' => $envio->oficina_id,
            'usuario_id' => $this->usuario['id'],
            'estatus_id'  => 2,
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
            'monto_total' => $this->total_pagar,
            'iva' => $this->iva,
        ]);

        foreach($this->pagos as $pago){
            FacturacionPago::create([
                'facturacion_id' => $facturacion->facturacion_id,
                'tipo_pago_id' => $pago['tipo_pago_id'],
                'monto' => $pago['monto'],
                'numero_referencia' => $pago['numero_referencia'],
            ]);
        }

        FacturacionEnvio::create([
            'facturacion_id' => $facturacion->facturacion_id,
            'envio_id' => $envio->envio_id,
            'usuario_id' => $envio->usuario_id,
            'oficina_id' => $envio->oficina_id,
            'servicio_id' => 22
        ]);

        FacturacionDetalle::create([
            'servicio_id' => 18,
            'facturacion_id' => $facturacion->facturacion_id,
            'monto' => $this->total_pagar
        ]);


            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró el envío Exporta Fácil ({$envio->envio_id}) con código ({$envio->codigo_envio})",
            ]);

            DB::commit();
            $this->dispatch('alertSuccess', message: 'Envio Registrado exitosamente!');
            $this->dispatch('envio_registrado');
        }catch(\Exception $e){
            // dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error, verifique los datos e intente de nuevo');
        };
    }


    public function render()
    {
        return view('livewire.exporta-facil.exporta-facil',['tipos_correos' => self::t_correo,]);
    }
}
