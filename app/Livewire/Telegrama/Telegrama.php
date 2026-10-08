<?php

namespace App\Livewire\Telegrama;

use Illuminate\Support\Str;
use App\Rules\CodigosTelefono;
use App\Notifications\TelegramaRecibidoNotification;
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
use App\Models\Parroquia;
use App\Models\Facturacion;
use App\Models\FacturacionPago;
use Livewire\Attributes\Layout;
use App\Models\FacturacionEnvio;
use App\Models\TelegramaRecibido;
use App\Models\FacturacionDetalle;
use Illuminate\Support\Facades\DB;
use App\Models\EnvioEncaminamiento;
use App\Models\FacturacionTarifaEnvio;
use App\Models\TarifaNacionalConcepto;
use App\Models\TipoRemitenteTelegrama;
use App\Models\LugarEmisionTelegrama;
use App\Models\CircuitoJudicialTribunalTelegrama;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class Telegrama extends Component
{
    public $usuario, $tipo_documento_rem, $documento_rem, $nombre_rem, $apellido_rem, $telefono_rem, $correo_rem,
        $tipo_documento_dest, $documento_dest, $nombre_dest, $apellido_dest, $estado_dest, $municipio_dest, $parroquia_dest,
        $ciudad_dest, $codigo_postal_dest, $telefono_dest, $correo_dest, $direccion_dest, $monto, $monto_servicio,
        $iva, $total, $metodo_pago_seleccionado, $monto_pagado, $mensaje_pago, $cliente_existe, $destinatario_existe,
        $tarifa_selec, $precio_total, $total_pagar, $codigo_git, $oficina_dest, $redaccion_telegrama, $bloques, $emision,
        $circuito_judicial_dest, $tipo_remitente, $lugar_emision, $total_palabras;

    public $parametro = ['Avenida' => '', 'Calle' => '', 'Edificio' => '', 'Nro Casa/Depa' => '', 'Punto de Ref' => ''];
    public $tipos_documentos = [];
    public $estado_oficina = [];
    public $estados = [];
    public $estados_dest = [];
    public $municipios = [];
    public $municipios_dest = [];
    public $parroquias = [];
    public $parroquias_dest = [];
    public $codigos_postales_rem = [];
    public $codigos_postales_dest = [];
    public $ciudades = [];
    public $metodos_pago = [];
    public $pagos = [];
    public $pago_registrados = [];
    public $tarifas = [];
    public $tarifas_selec = [];
    public $tarifas_detalle = [];
    public $tipo_telegrama = 4;
    public $oficina = [];
    public $oficinas = [];
    public $cantidad_palabras = 0;
    public $cantidad_palabras_reales = 0;
    public $tiposRemitente = [];
    public $lugaresEmision = [];
    public $centrosJudiciales = [];
    public $cj_dest = [];
    public $mostrar_caja = false;

    public function rules()
    {
        return [
            'tipo_documento_rem' => 'required',
            'documento_rem' => 'required|digits_between:6,12',
            'nombre_rem' => 'required|max:60',
            'apellido_rem' => 'required|max:60',
            'telefono_rem' => ['required', new CodigosTelefono],
            'correo_rem' => 'required|email',
            'tipo_documento_dest' => 'required',
            'documento_dest' => 'required|digits_between:6,12',
            'nombre_dest' => 'required|max:60',
            'apellido_dest' => 'required|max:60',
            'estado_dest' => 'required',
            'telefono_dest' => ['required', new CodigosTelefono],
            'correo_dest' => 'required|email',
            'redaccion_telegrama' => 'required|regex:/^(\S+\s+){6,}\S+\s*$/',
            'direccion_dest' => 'required'
        ];
    }

    protected $messages = [
        'tipo_documento_rem.required' => 'El campo es requerido',
        'documento_rem.required' => 'El campo es requrido',
        'documento_rem.digits_between' => 'El valor debe ser de tipo numerico y tener entre 6 y 12 digitos',
        'nombre_rem.required' => 'El campo es requerido',
        'nombre_rem.max' => 'Solo un maximo de 60 caracteres',
        'apellido_rem.required' => 'El campo es requerido',
        'apellido_rem.max' => 'Solo un maximo de 60 caracteres',
        'telefono_rem.required' => 'El campo es requerido',
        'correo_rem.required' => 'El campo es requerido',
        'correo_rem.email' => 'Debe estar en un formato valido para correo',

        'tipo_documento_dest.required' => 'El campo es requerido',
        'documento_dest.required' => 'El campo es requrido',
        'documento_dest.digits_between' => 'El valor debe ser de tipo numerico y tener entre 6 y 12 digitos',
        'nombre_dest.required' => 'El campo es requerido',
        'nombre_dest.max' => 'Solo un maximo de 60 caracteres',
        'apellido_dest.required' => 'El campo es requerido',
        'apellido_dest.max' => 'Solo un maximo de 60 caracteres',
        'estado_dest.required' => 'El campo es requerido',
        'telefono_dest.required' => 'El campo es requerido',
        'correo_dest.required' => 'El campo es requerido',
        'correo_dest.email' => 'Debe estar en un formato valido para correo',
        'redaccion_telegrama.regex' => 'El telegrama debe tener un minimo de 7 palabras',
    ];


    public function mount()
    {
        $this->usuario = auth()->user();
        $this->oficina = Oficina::where('oficina_id', $this->usuario['oficina_id'])->first();
        $this->estado_oficina = Estado::where('estado_id', $this->oficina['estado_id'])->first();
        $this->estados_dest = Estado::where('pais_id', 90)->get();
        $this->metodos_pago = TipoPago::all();
        $this->tarifas = TarifaNacionalConcepto::where('servicios_id', 3)->whereNotIn('tarifa_conceptos_id', [4, 5, 6])->get();
        $this->tipos_documentos = Documento::all();
        $this->tiposRemitente = TipoRemitenteTelegrama::all();
        $this->lugaresEmision = LugarEmisionTelegrama::orderBy('nombre')->get();
        $this->centrosJudiciales = collect();
        $this->cj_dest = CircuitoJudicialTribunalTelegrama::where('lugar_emision_telegramas_id', 1)->get();
        $this->updatedTipoTelegrama($this->tipo_telegrama);
        $this->CantidadPalabras('0');
    }

    public function updatedEmision($value)
    {
        $this->lugar_emision = null;
        $this->centrosJudiciales = collect();

        if ($value === '' || $value === null) {
            return;
        }

        $this->centrosJudiciales = CircuitoJudicialTribunalTelegrama::where(
            'lugar_emision_telegramas_id',
            $value
        )
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function contabilizar_palabras($value)
    {
        $limpio = preg_replace('/\s+/', ' ', trim($value));
        $palabras = explode(' ', $limpio);

        $contadorTasable = 0;
        $contadorReal = 0;

        foreach ($palabras as $palabra) {
            if ($palabra === '') {
                continue;
            }

            $longitud = mb_strlen($palabra);
            $contadorTasable += ($longitud >= 12) ? 2 : 1;

            $palabraLimpia = trim($palabra, ".,;:!¡?¿\"'@#\$%&*()[]{}<>");

            // Si queda vacía o no tiene letras ni números, omitimos para conteo real
            if ($palabraLimpia === '' || !preg_match('/[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ]/u', $palabraLimpia)) {
                continue;
            }
            $contadorReal++;
        }

        $this->cantidad_palabras = $contadorTasable;
        $this->cantidad_palabras_reales = $contadorReal;

        $this->CantidadPalabras($this->cantidad_palabras);
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

    public function Habilitar()
    {
        $this->validate();
        if ($this->monto_pagado > 0 && $this->monto_pagado >= $this->total_pagar) {
            $this->submit();
        } else {
            return $this->dispatch('alertSuccess2', message: 'Error, el monto pagado no coincide con el total a pagar');
        }
    }


    public function updatedDocumentoRem()
    {
        $this->documento_rem = preg_replace('/[^\d]/', '', $this->documento_rem);
        if (!empty($this->documento_rem)) {
            $cliente = Cliente::where('numero_documento', $this->documento_rem)->first();

            if ($cliente) {
                $this->nombre_rem = $cliente->nombre;
                $this->apellido_rem = $cliente->apellido;
                $this->tipo_documento_rem = $cliente->tipo_documento;
                $this->telefono_rem = $cliente->telefono;
                $this->correo_rem = $cliente->correo;
                $this->cliente_existe = 1;
            } else {
                $this->cliente_existe = 0;
            }
        }
    }


    public function updatedDocumentoDest()
    {
        $this->documento_dest = preg_replace('/[^\d]/', '', $this->documento_dest);
        if (!empty($this->documento_dest)) {
            $cliente = Cliente::where('numero_documento', $this->documento_dest)->first();

            if ($cliente) {
                $this->nombre_dest = $cliente->nombre;
                $this->apellido_dest = $cliente->apellido;
                $this->tipo_documento_dest = $cliente->tipo_documento;
                $this->telefono_dest = $cliente->telefono;
                $this->correo_dest = $cliente->correo;
                $this->destinatario_existe = 1;
            } else {
                $this->destinatario_existe = 0;
            }
        }
    }

    public function CantidadPalabras($value)
    {
        $this->cantidad_palabras = $value;

        // Solo cuenta bloques completos de 50 palabras
        $this->bloques = intdiv($value, 50);

        // Agrega tarifa 6 solo si hay al menos 1 bloque (es decir, 50 o más palabras)
        if ($this->bloques > 0 && !in_array(6, $this->tarifas_selec)) {
            $this->tarifas_selec[] = 6;
        }

        // Elimina tarifa 6 si las palabras bajan de 50
        if ($value < 50) {
            $this->tarifas_selec = array_values(array_filter($this->tarifas_selec, fn($tarifa) => $tarifa != 6));
        }

        // Actualiza bloques en el cálculo de tarifas
        $this->recalcularTarifasDetalle();
        $this->updatedTarifasSelec();
    }



    public function updatedTipoTelegrama($value)
    {
        // Elimina cualquier tarifa relacionada a tipo_telegrama (4 o 5)
        $this->tarifas_selec = array_values(array_filter(
            $this->tarifas_selec,
            fn($id) => !in_array($id, [4, 5])
        ));

        // Agrega la nueva tarifa seleccionada (4 u 5), sin duplicar
        if (!in_array($value, $this->tarifas_selec)) {
            $this->tarifas_selec[] = (int)$value;
        }

        // Telegrama Urgente exige Petición de Entrega (id 7): se marca automáticamente y
        // queda fija (la vista la deshabilita mientras el tipo sea Urgente). Al volver a
        // Ordinario la casilla se rehabilita pero conserva su selección.
        if ($value == 5 && !in_array(7, $this->tarifas_selec)) {
            $this->tarifas_selec[] = 7;
        }

        $this->updatedTarifasSelec();
    }

    public function recalcularTarifasDetalle()
    {
        $this->tarifas_detalle = [];

        foreach ($this->tarifas_selec as $id) {
            $tarifa = TarifaNacionalConcepto::find($id);
            if (!$tarifa) continue;

            if ($id == 6) {
                $monto = $tarifa->monto * $this->bloques;
            } elseif (in_array($id, [4, 5])) {
                $monto = $tarifa->monto * $this->cantidad_palabras;
            } else {
                $monto = $tarifa->monto;
            }

            $this->tarifas_detalle[] = [
                'id' => $id,
                'nombre' => $tarifa->nombre,
                'monto' => $monto,
            ];
        }
    }


    public function updatedTarifasSelec()
    {
        $this->recalcularTarifasDetalle();
        if (empty($this->tarifas_detalle)) {
            $this->tarifa_selec = collect();
            $this->precio_total = 0;
            $this->iva = 0;
            $this->total_pagar = 0;
        } else {
            // Convertimos tarifas_detalle a una colección para facilitar el manejo
            $this->tarifa_selec = collect($this->tarifas_detalle);

            // Sumamos el monto total real ya calculado por CantidadPalabras()
            $this->precio_total = bcdiv($this->tarifa_selec->sum('monto'), 1, 2);

            if ($this->oficina['zona_economica_especial'] == false) {
                $this->iva = bcdiv($this->precio_total * 0.16, 1, 2);
            } else {
                $this->iva = 0;
            }
            $this->total_pagar = bcdiv(($this->precio_total + $this->iva), 1, 2);
        }
    }


    public function updatedEstadoDest()
    {
        $this->oficinas = [];

        if ($this->estado_dest == '') {
            $this->oficinas = [];
        } else {
            $this->oficinas = Oficina::where('estado_id', $this->estado_dest)->whereIn('tipo_oficina_id', [1, 2, 3])->get();
        }
    }


    public function updatedOficinaDest($value)
    {
        $of = Oficina::where('oficina_id', $value)->first();

        $this->municipio_dest = $of->municipio_id;
        $this->parroquia_dest = $of->parroquia_id;
        $this->codigo_postal_dest = $of->codigo_ubicacion;
    }


    public function actualizarTarifa($tarifa_id)
    {
        $tarifa = TarifaNacionalConcepto::find($tarifa_id);

        if ($tarifa && $tarifa->exclusion) {
            if (in_array($tarifa->exclusion, $this->tarifas_selec)) {
                $this->tarifas_selec = array_diff($this->tarifas_selec, [$tarifa->exclusion]);
            }
        }
    }

    #[\Livewire\Attributes\On('montoPagadoActualizado')]
    public function montoPagadoActualizado($montoPagado, $pagos)
    {
        $this->monto_pagado = $montoPagado;
        $this->pagos = $pagos;
    }

    public function calcular_palabras_totales()
    {
        $this->validate();

        $tipo_remitente = TipoRemitenteTelegrama::where('tipos_remitente_telegramas_id', $this->tipo_remitente)->value('nombre');
        $lugar_emision = LugarEmisionTelegrama::where('lugar_emision_telegramas_id', $this->emision)->value('nombre');
        $lugar_circuito = CircuitoJudicialTribunalTelegrama::where('circuito_judicial_tribunal_telegramas_id', $this->lugar_emision)->value('nombre');
        $estado_dest = Estado::where('estado_id', $this->estado_dest)->value('nombre');
        $oficina_dest = Oficina::where('oficina_id', $this->oficina_dest)->value('nombre');
        $circuito_dest = CircuitoJudicialTribunalTelegrama::where('circuito_judicial_tribunal_telegramas_id', $this->circuito_judicial_dest)->value('nombre');

        $palabras = trim("{$this->tipo_documento_rem} {$this->documento_rem} {$tipo_remitente} {$lugar_emision} {$lugar_circuito} {$this->nombre_rem} 
            {$this->apellido_rem} {$this->telefono_rem} {$this->correo_rem} {$this->oficina['nombre']} {$this->tipo_documento_dest} {$this->documento_dest}
            {$this->nombre_dest} {$this->apellido_dest} {$estado_dest} {$oficina_dest} {$this->telefono_dest} {$this->correo_dest} {$circuito_dest} 
            {$this->direccion_dest} {$this->redaccion_telegrama}");

        $this->contabilizar_palabras($palabras);

        $this->mostrar_caja = true;
    }

    public function generarCodigoEnvio(): string
    {
        $codigoEstado = $this->estado_oficina['codigo'];
        $codigoOficina = $this->oficina['codigo'];
        // Prefijo base para filtrar en la BD, por ejemplo: MR-OP001-
        $prefijo = "{$codigoEstado}-{$codigoOficina}-";

        // Buscar el último envío con ese prefijo. Ordenamos por envio_id (entero
        // autoincremental), no por codigo_envio: ordenar el código como texto haría que
        // "...-9" se considerara mayor que "...-10", devolviendo un correlativo equivocado.
        $ultimo = Envio::where('codigo_envio', 'like', $prefijo . '%')
            ->orderByDesc('envio_id')
            ->first();

        if ($ultimo) {
            // Extraer la parte numérica final después del último guion
            $partes = explode('-', $ultimo->codigo_envio);
            $ultimoNumero = (int) end($partes);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        // Construir el nuevo código
        return "{$codigoEstado}-{$codigoOficina}-{$nuevoNumero}";
    }


    public function submit()
    {
        $this->codigo_git = $this->generarCodigoEnvio();
        DB::beginTransaction();

        try {
            if ($this->cliente_existe == 0) {
                //Guardar cliente remitente
                Cliente::Create([
                    'numero_documento' => $this->documento_rem,
                    'nombre' => $this->nombre_rem,
                    'apellido' => $this->apellido_rem,
                    'tipo_documento' => $this->tipo_documento_rem,
                    'telefono' => $this->telefono_rem,
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
                    'telefono' => $this->telefono_dest,
                    'correo' => $this->correo_dest,
                ]);
            }

            $envio = Envio::create([
                'servicio_id' => 3,
                'tipo_envio' => 'nacional',
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'nombre_rem' => $this->nombre_rem,
                'apellido_rem' => $this->apellido_rem,
                'tipo_documento_rem' => $this->tipo_documento_rem,
                'documento_rem' => $this->documento_rem,
                'codigo_postal_rem' => $this->oficina['codigo_ubicacion'],
                'estado_rem' => $this->oficina['estado_id'],
                'municipio_rem' => $this->oficina['municipio_id'],
                'parroquia_rem' => $this->oficina['parroquia_id'],
                'ciudad_rem' => $this->oficina['ciudad_id'] ?? 'N/A',
                'direccion_rem' => $this->oficina['direccion'],
                'correo_rem' => $this->correo_rem,
                'telefono_rem' => $this->telefono_rem,
                'nombre_dest' => $this->nombre_dest,
                'apellido_dest' => $this->apellido_dest,
                'tipo_documento_dest' => $this->tipo_documento_dest,
                'documento_dest' => $this->documento_dest,
                'codigo_postal_dest' => $this->codigo_postal_dest,
                'continente_dest' => $this->continente ?? null,
                'pais_dest' => $this->pais ?? null,
                'estado_dest' => $this->estado_dest,
                'municipio_dest' => $this->municipio_dest,
                'parroquia_dest' => $this->parroquia_dest,
                'ciudad_dest' => $this->ciudad_dest ?? null,
                'direccion_dest' => $this->direccion_dest,
                'oficina_dest_id' => $this->oficina_dest,
                'tlf_dest' => $this->telefono_dest,
                'correo_dest' => $this->correo_dest,
                'servicio_expreso' => $this->seb ?? null,
                'peso' => $this->peso ?? null,
                'coste' => $this->monto_pagado,
                'contenido' => 'N/A',
                'apartado_postal' => null,
                'codigo_envio' => $this->codigo_git,
                'devolucion' => false,
                'descubierto' => false,
                'tipo_saca_id' => $this->tipo_saca ?? null,
                'coste_sin_iva' => $this->precio_total,
            ]);

            TelegramaRecibido::create([
                'envio_id' => $envio->envio_id,
                'recibido' => false,
                'contenido_telegrama' => $this->redaccion_telegrama,
                'palabras_tasables' => $this->cantidad_palabras,
                'palabras_reales' => $this->cantidad_palabras_reales,
                'tipo_remitente' => $this->tipo_remitente ?? null,
                'lugar_emision_rem' => $this->emision ?? null,
                'sitio_especifico_emision' => $this->lugar_emision ?? null,
                'circuito_judicial_dest' => $this->circuito_judicial_dest ?? null,
            ]);

            // Notificar a la oficina destino (badge del header). Dentro de la transacción:
            // si el envío hace rollback, la notificación también se revierte.
            $oficinaDestino = Oficina::find($this->oficina_dest);
            if ($oficinaDestino) {
                $oficinaDestino->notify(new TelegramaRecibidoNotification($envio));
            }

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

            foreach ($this->pagos as $pago) {
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
                'oficina_id' => $this->usuario['oficina_id'],
                'usuario_id' => $this->usuario['id'],
                'servicio_id' => 3
            ]);

            FacturacionDetalle::create([
                'servicio_id' => $envio->servicio_id,
                'facturacion_id' => $facturacion->facturacion_id,
                'monto' => $this->total_pagar
            ]);

            foreach ($this->tarifas_selec as $tarifa) {
                FacturacionTarifaEnvio::create([
                    'facturacion_id' => $facturacion->facturacion_id,
                    'envio_id' => $envio->envio_id,
                    'tipo_envio' => $envio->tipo_envio,
                    'servicio_id' => $envio->servicio_id,
                    'tarifa_id' => $tarifa,
                ]);
            }


            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se registró el telegrama envío ({$envio->envio_id}) con código ({$envio->codigo_envio})",
            ]);

            DB::commit(); // Si todo sale bien, confirmamos la transacción
            $this->dispatch('alertSuccess', message: 'Envio Creado exitosamente!');
            $this->dispatch('servicioAceptado');
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Ocurrio un error en la creacion del envio, verifique los datos e intente de nuevo');
            return;
            // Si ocurre un error, revertimos todos los cambios
        }
    }

    public function render()
    {
        return view('livewire.telegrama.telegrama');
    }
}
