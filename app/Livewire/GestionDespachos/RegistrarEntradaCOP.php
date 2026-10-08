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
class RegistrarEntradaCOP extends Component
{
    public $codigoEnvioBusqueda = '';
    public $mensajeBusqueda = '';
    public $enviosIds = [];
    public $enviosAcumulados = [];
    public $sacasIds = [];          // saca(s) del codigo escaneado en esta busqueda
    public $sacasAcumuladas = [];   // sacas escaneadas para registrar su encaminamiento de entrada
    public $sacasDetalles = [];     // datos de las sacas acumuladas para mostrar en el carrito
    public $enviosDetalles = [];
    public $externa;
    public $viaje_id;

    public function updatedCodigoEnvioBusqueda()
    {
        $this->codigoEnvioBusqueda = preg_replace("/'/", '-', $this->codigoEnvioBusqueda);
        $this->enviosIds = [];
        $this->sacasIds = [];

        if (!empty($this->codigoEnvioBusqueda)) {
            $envio = Envio::where('codigo_envio', $this->codigoEnvioBusqueda)->first();
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
        $this->enviosAcumulados = array_unique(array_merge($this->enviosAcumulados, $this->enviosIds));
        $this->sacasAcumuladas = array_unique(array_merge($this->sacasAcumuladas, $this->sacasIds));
        $this->actualizarEnviosDetalles();
        $this->codigoEnvioBusqueda = '';
        $this->mensajeBusqueda = '';
        $this->enviosIds = [];
        $this->sacasIds = [];
    }

    public function actualizarEnviosDetalles()
    {
        $this->enviosDetalles = Envio::whereIn('envio_id', $this->enviosAcumulados)->get();
        $this->sacasDetalles = Saca::with('tipoSaca')
            ->whereIn('saca_id', $this->sacasAcumuladas)
            ->get();
        $this->viaje_id = EnvioEncaminamiento::whereIn('envio_id', $this->enviosAcumulados)
            ->whereNotIn('estatus_id', [1, 2])
            ->orderBy('created_at', 'desc')
            ->pluck('viaje_id')
            ->first();
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

        $usuario = auth()->user();

        $estatusMap = [
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 8
        ];

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
            $exists = EnvioAlmacen::where('envio_id', $envio_id)
                ->where('oficina_id', $usuario->oficina_id)
                ->where('estatus', true)
                ->exists();

            if ($exists) {
                $this->dispatch('alertError', message: 'El envío ya está registrado en el almacén con la misma oficina y estatus.');
                return;
            }

            EnvioEncaminamiento::create([
                'envio_id'          => $envio_id,
                'oficina_id'        => $usuario->oficina_id,
                'oficina_externa_id'=> $origenes[$envio_id]['oficina_externa_id'],
                'usuario_id'        => $usuario->id,
                'viaje_id'          => $origenes[$envio_id]['viaje_id'] ?? null,
                'estatus_id'        => $origenes[$envio_id]['estatus_id'],
                'devolucion'        => false,
            ]);

            $codigo = $envios[$envio_id] ?? null;

            EnvioAlmacen::create([
                'oficina_id'        => $usuario->oficina_id,
                'envio_id'          => $envio_id,
                'codigo'            => $codigo,
                'saca_id'           => null,
                'estatus'           => true,
                'Entrada'           => now()->toDateTimeString(),
            ]);
        }

        // Desactivar todos los envíos asociados a las sacas de los envíos procesados
        // Excepto cuando la oficina receptora reexpide (COP): la saca sigue cerrada
        // y debe poder salir nuevamente hacia su destino final.
        $tipoOficinaReceptora = (int) Oficina::where('oficina_id', $usuario->oficina_id)->value('tipo_oficina_id');
        $reexpide = in_array($tipoOficinaReceptora, Saca::TIPOS_OFICINA_REEXPIDEN, true);

        if (!$reexpide && !empty($this->enviosAcumulados)) {
            $sacaIds = EnvioSaca::whereIn('envio_id', $this->enviosAcumulados)->pluck('saca_id')->unique();

            foreach ($sacaIds as $sacaId) {
                EnvioSaca::where('saca_id', $sacaId)
                    ->update(['activo' => false]);
            }
        }

        // Registrar el encaminamiento de RECEPCION de cada valija escaneada.
        //
        // Va FUERA del bloque de arriba a proposito: la valija cambia de ubicacion
        // siempre que se recibe, sin importar el tipo de oficina receptora ni si
        // trae envios. Antes estaba dentro y por eso ni los COP ni las valijas de
        // peso registraban su entrada, quedando "en transito" para siempre.
        //
        // El estatus depende de quien recibe: un COP la deja RECIBIDA (cerrada,
        // reexpedible); cualquier otra oficina la deja ABIERTA (estado final).
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
            'descripcion' => "Se registró entrada en COP de " . count($this->enviosAcumulados) . " envío(s) desde la oficina externa ({$this->externa})",
        ]);

        $this->dispatch('alertSuccess', message: 'Entrada registrada exitosamente!');
        $this->dispatch('tarifaUpdated');
    }
    

    public function render()
    {
        return view('livewire.gestion-despachos.registrar-entrada-c-o-p');
    }
}
