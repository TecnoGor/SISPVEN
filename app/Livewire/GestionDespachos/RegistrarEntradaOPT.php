<?php

namespace App\Livewire\GestionDespachos;

use Livewire\Attributes\Layout;
use App\Models\Envio;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioSaca;
use App\Models\Oficina;
use App\Models\Saca;
use App\Models\SacaEncaminamiento;
use App\Models\UsuarioSeguimiento;
use Livewire\Component;

#[Layout('layouts.app')]
class RegistrarEntradaOPT extends Component
{
    public $codigoEnvioBusqueda = '';
    public $mensajeBusqueda = '';
    public $enviosIds = []; // IDs obtenidos de una búsqueda
    public $enviosAcumulados = []; // IDs acumulados al presionar "Agregar"
    public $sacasIds = [];          // saca(s) del codigo escaneado en esta busqueda
    public $sacasAcumuladas = [];   // sacas escaneadas para registrar su encaminamiento de entrada
    public $sacasDetalles = [];     // datos de las sacas acumuladas para mostrar en el carrito
    public $enviosDetalles = []; // Información detallada de los envíos acumulados
    public $externa; // Declaramos la variable a nivel de clase
    public $viaje_id;

    public function updatedCodigoEnvioBusqueda()
    {
        $this->codigoEnvioBusqueda = preg_replace("/'/", '-', $this->codigoEnvioBusqueda);

        $this->enviosIds = []; // Limpiar resultados previos
        $this->sacasIds = [];

        if (!empty($this->codigoEnvioBusqueda)) {
            // Buscar en la tabla de envíos
            $envio = Envio::where('codigo_envio', $this->codigoEnvioBusqueda)->first();

            // Buscar en la tabla de sacas
            $saca = Saca::where('codigo_saca', $this->codigoEnvioBusqueda)->first();

            if ($envio) {
                $this->mensajeBusqueda = 'Envío conseguido';
                $this->enviosIds = [$envio->envio_id];
            } elseif ($saca) {
                $this->mensajeBusqueda = 'Valija conseguida';
                // Guardar la saca para registrar su encaminamiento de entrada, aunque no
                // tenga envios (caso valija de peso). Sus envios, si los hay, igual entran.
                $this->sacasIds = [$saca->saca_id];
                $this->enviosIds = EnvioSaca::where('saca_id', $saca->saca_id)->pluck('envio_id')->toArray();
            } else {
                $this->mensajeBusqueda = 'El código no existe';
            }
        } else {
            $this->mensajeBusqueda = '';
        }
    }

    public function agregarEnvios()
    {
        // Agregar los envíos obtenidos a la lista acumulativa
        $this->enviosAcumulados = array_unique(array_merge($this->enviosAcumulados, $this->enviosIds));
        $this->sacasAcumuladas = array_unique(array_merge($this->sacasAcumuladas, $this->sacasIds));

        // Actualizar los detalles de los envíos
        $this->actualizarEnviosDetalles();

        // Limpiar la búsqueda
        $this->codigoEnvioBusqueda = '';
        $this->mensajeBusqueda = '';
        $this->enviosIds = [];
        $this->sacasIds = [];
    }

    public function actualizarEnviosDetalles()
    {
        // Obtener los detalles de los envíos acumulados
        $this->enviosDetalles = Envio::whereIn('envio_id', $this->enviosAcumulados)->get();
        $this->sacasDetalles = Saca::with('tipoSaca')
            ->whereIn('saca_id', $this->sacasAcumuladas)
            ->get();

        // Obtener viaje_id del último encaminamiento válido
        $this->viaje_id = EnvioEncaminamiento::whereIn('envio_id', $this->enviosAcumulados)
            ->whereNotIn('estatus_id', [1, 2])
            ->orderBy('created_at', 'desc')
            ->pluck('viaje_id')
            ->first();

        // Obtener oficina_id del último encaminamiento válido
        $this->externa = EnvioEncaminamiento::whereIn('envio_id', $this->enviosAcumulados)
            ->whereNotIn('estatus_id', [1, 2])
            ->orderBy('created_at', 'desc')
            ->pluck('oficina_id')
            ->first();

        // Si no hay envios (p.ej. solo valijas de peso), determinar la oficina de
        // procedencia desde el ultimo encaminamiento EN TRANSITO de las sacas escaneadas.
        if (!$this->externa && !empty($this->sacasAcumuladas)) {
            $this->externa = SacaEncaminamiento::whereIn('saca_id', $this->sacasAcumuladas)
                ->where('saca_estatus_id', Saca::ESTATUS_TRANSITO)
                ->orderByDesc('saca_encaminamiento_id')
                ->value('oficina_externa_id');
        }
    }
    public function create()
    {
        // Validar que todos los envíos tengan un estatus de salida previo
        $estatusSalida = [9, 10, 11, 12, 13, 14];

        foreach ($this->enviosAcumulados as $envio_id) {
            $ultimoEstatus = EnvioEncaminamiento::where('envio_id', $envio_id)
                ->orderBy('envios_encaminamiento_id', 'desc')
                ->first();

            if (!$ultimoEstatus || !in_array($ultimoEstatus->estatus_id, $estatusSalida)) {
                $codigo = Envio::where('envio_id', $envio_id)->value('codigo_envio');
                $this->dispatch('alertError', message: "El envío {$codigo} no posee un estatus de salida previo.");
                return;
            }
        }

        // Obtener usuario autenticado
        $usuario = auth()->user();

        // Definir el mapeo de tipo_oficina a estatus_id
        $estatusMap = [
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 8
        ];

        // Obtener los códigos de los envíos antes del ciclo para evitar consultas repetidas
        $envios = Envio::whereIn('envio_id', $this->enviosAcumulados)->pluck('codigo_envio', 'envio_id');

        // Resolver el origen de cada envío por separado. Un lote puede contener
        // envíos que vienen de oficinas distintas, por lo que el origen, el viaje
        // y el estatus deben derivarse del último encaminamiento de cada uno.
        $origenes = [];

        foreach ($this->enviosAcumulados as $envio_id) {
            $ultimoEncaminamiento = EnvioEncaminamiento::where('envio_id', $envio_id)
                ->whereNotIn('estatus_id', [1, 2])
                ->orderBy('envios_encaminamiento_id', 'desc')
                ->first();

            $tipo_oficina = Oficina::where('oficina_id', $ultimoEncaminamiento?->oficina_id)
                ->value('tipo_oficina_id');

            if (!array_key_exists($tipo_oficina, $estatusMap)) {
                $codigo = $envios[$envio_id] ?? null;
                $this->dispatch('alertError', message: "El envío {$codigo} proviene de una oficina sin tipo válido.");
                return;
            }

            $origenes[$envio_id] = [
                'oficina_externa_id' => $ultimoEncaminamiento->oficina_id,
                'viaje_id'           => $ultimoEncaminamiento->viaje_id,
                'estatus_id'         => $estatusMap[$tipo_oficina],
            ];
        }

        foreach ($this->enviosAcumulados as $envio_id) {
            // Verificar si el registro ya existe en EnvioAlmacen
            $exists = EnvioAlmacen::where('envio_id', $envio_id)
                ->where('oficina_id', $usuario->oficina_id)
                ->where('estatus', true)
                ->exists();

            // Si el registro ya existe, mostramos un mensaje de error
            if ($exists) {
                $this->dispatch('alertError', message: 'El envío ya está registrado en el almacén con la misma oficina y estatus.');
                return; // Salir del método para evitar la creación de nuevos registros
            }

            // Crear EnvioEncaminamiento para cada envio_id
            EnvioEncaminamiento::create([
                'envio_id'          => $envio_id,
                'oficina_id'        => $usuario->oficina_id,
                'oficina_externa_id' => $origenes[$envio_id]['oficina_externa_id'],
                'usuario_id'        => $usuario->id,
                'viaje_id'          => $origenes[$envio_id]['viaje_id'] ?? null,
                'estatus_id'        => $origenes[$envio_id]['estatus_id'], // Asignamos el estatus según el tipo de oficina de origen
                'devolucion'        => false, // Asignamos devolucion como false
            ]);

            // Obtener el código de envío de la colección de envíos precargados
            $codigo = $envios[$envio_id] ?? null;

            // Crear EnvioAlmacen para cada envio_id
            EnvioAlmacen::create([
                'oficina_id'        => $usuario->oficina_id,
                'envio_id'          => $envio_id,
                'codigo'            => $codigo,
                'saca_id'           => null,
                'estatus'           => true,
                'Entrada'           => now()->toDateTimeString(),
            ]);
        }

        // Actualizar todos los envíos asociados a la saca a 'activo' => false
        if (!empty($this->enviosAcumulados)) {
            $sacaIds = EnvioSaca::whereIn('envio_id', $this->enviosAcumulados)->pluck('saca_id')->unique();

            foreach ($sacaIds as $sacaId) {
                // Desactivar todos los envíos asociados a esta saca
                EnvioSaca::where('saca_id', $sacaId)
                    ->update(['activo' => false]);
            }
        }

        // Registrar el encaminamiento de RECEPCION de cada valija escaneada.
        //
        // Va FUERA del bloque de arriba a proposito: la valija cambia de ubicacion
        // siempre que se recibe, traiga envios o no. Antes estaba dentro del if y
        // por eso las valijas de peso nunca registraban su entrada, quedando "en
        // transito" para siempre pese al comentario que decia lo contrario.
        //
        // El estatus se deriva del tipo de oficina receptora en vez de asumir que
        // toda entrada por este componente es una OPT: si un COP lo usara, la
        // valija quedaria ABIERTA por error y no podria reexpedirse.
        $tipoOficinaReceptora = (int) Oficina::where('oficina_id', $usuario->oficina_id)->value('tipo_oficina_id');
        $estatusRecepcion = Saca::estatusAlRecibirEn($tipoOficinaReceptora);

        foreach ($this->sacasAcumuladas as $sacaId) {
            SacaEncaminamiento::create([
                'saca_id'            => $sacaId,
                'oficina_id'         => $usuario->oficina_id,   // donde se recibe
                'oficina_externa_id' => $this->externa,         // de donde vino
                'saca_estatus_id'    => $estatusRecepcion,
                'usuario_id'         => $usuario->id,
            ]);
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se registró entrada en OPT de " . count($this->enviosAcumulados) . " envío(s) desde la oficina externa ({$this->externa})",
        ]);

        // Enviar el mensaje de éxito al finalizar
        $this->dispatch('alertSuccess', message: 'Entrada registrada exitosamente!');
        $this->dispatch('tarifaUpdated');
    }



    public function render()
    {
        return view('livewire.gestion-despachos.registrar-entrada-o-p-t');
    }
}
