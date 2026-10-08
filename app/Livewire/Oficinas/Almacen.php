<?php

namespace App\Livewire\Oficinas;

use App\Exports\EnviosExport;
use App\Livewire\Forms\Almacen\CreateForm;
use App\Models\AlmacenAviso;
use App\Models\Envio;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEncaminamiento;
use App\Models\IntentoEntrega;
use App\Models\TipoPago;
use App\Models\User;
use App\Models\UsuarioSeguimiento;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class Almacen extends Component
{
    use WithPagination;

    public $registro = [];
    public $usuario;
    public $search;
    public $filterStatus = true;
    public $perPage = 5;
    public $sortDir = 'ASC';
    public $sortBy = 'envio_almacen_id';
    public $mostrar_modal = false;
    public $mostrar_modal_asignar = false;
    public $intento = false;
    public $devoluciones = 0;
    public $origen = 0; // 0 = todos, 1 = creados en esta oficina, 2 = recibidos desde otra OPT
    public $cantidad_avisos = 0;
    public $cantidad_intentos = 0;

    public $carteros = [];
    public $cartero_seleccionado = '';
    public $nombre_cartero;
    public $selected_envio; // Este será un objeto del modelo EnvioAlmacen

    public $solo_visualizar = false;

    /*//////////////////// Envío en lote a salas /////////////////*/
    public $seleccionados = [];
    public $mostrar_modal_sala = false;
    public $sala_destino = null;

    // Input para escanear/escribir códigos y agregarlos al lote.
    public string $codigo_lote = '';
    public ?string $mensaje_codigo = null;
    public ?string $mensaje_codigo_tipo = null; // 'success' | 'error' | 'warning'

    // Bandeja de aceptación: envíos internacionales devueltos desde Análisis (estatus 38).
    public bool $mostrar_modal_apertura = false;
    public array $seleccionados_apertura = [];

    // Servicios y estatus dinámicos para Expedición.
    // Bulto/EMS/Iposplus tienen sus propios estatus; el resto cae en el genérico.
    const SERVICIO_BULTO    = 23;
    const SERVICIO_EMS      = 21;
    const SERVICIO_IPOSPLUS = 10;
    const ESTATUS_SALIDA_EXPEDICION_BULTO    = 23;
    const ESTATUS_SALIDA_EXPEDICION_EMS      = 35;
    const ESTATUS_SALIDA_EXPEDICION_IPOSPLUS = 39;
    const ESTATUS_SALIDA_EXPEDICION_GENERICO = 41;

    // Apertura: bandeja de aceptación de envíos devueltos desde Análisis.
    const ESTATUS_SALIDA_HACIA_APERTURA = 38;
    const ESTATUS_ENTRADA_APERTURA      = 37;

    // Servicios del Servicio Postal Universal. Ya no restringen a qué sala puede ir un
    // envío (Distribución los exigía y Expedición los rechazaba); se conservan como
    // referencia de los IDs.
    const SERVICIO_SPU_NACIONAL      = 1;
    const SERVICIO_SPU_INTERNACIONAL = 14;
    const SERVICIO_SACAS_M           = 18;
    const ESTATUS_SALIDA_DISTRIBUCION = 19;

    // Catálogo de salas disponibles para enviar desde Almacén.
    // 'oficinas_con_sala' = null  → disponible en todas las oficinas
    // 'oficinas_con_sala' = array → solo disponible en esas oficinas (por oficina_id)
    // 'estatus_id' = null         → estatus dinámico, se decide en enviarASala() según el envío.
    public array $salas_disponibles = [
        'unidad_analisis' => [
            'nombre'            => 'Unidad de Análisis de Devolución',
            'estatus_id'        => 21,
            'oficinas_con_sala' => [10], // Solo CPC
        ],
        'aduana' => [
            'nombre'            => 'Aduana',
            'estatus_id'        => 7, // "Entrada a Aduana"
            'oficinas_con_sala' => [10], // TODO: agregar IDs de las demás oficinas con Aduana cuando se tenga la lista
        ],
        'expedicion' => [
            'nombre'            => 'Expedición',
            'estatus_id'        => null, // Dinámico: 23 Bulto, 35 EMS, 39 Iposplus, 41 el resto (ver enviarASala).
            'oficinas_con_sala' => [10], // Solo CPC
        ],
        'distribucion' => [
            'nombre'            => 'Distribución Paquetes Muestras',
            'estatus_id'        => 19,
            'oficinas_con_sala' => [10], // Solo CPC
        ],
    ];
    /*//////////////////// Envío en lote a salas /////////////////*/

    public CreateForm $createForm;

    public function mount()
    {
        $this->usuario = auth()->user();

        //Traeme a los usuarios con el rol de Cartero que pertenezcan a la oficina del usuario autenticado y que estén activos
        $this->carteros = User::whereHas('roles', function ($query) {
            $query->where('id', 7);
        })
            ->where('oficina_id', auth()->user()->oficina_id)
            ->where('activo', true)
            ->get();
    }

    public function toggleStatus()
    {
        $this->filterStatus = !$this->filterStatus;
        $this->seleccionados = [];
        $this->resetPage();
    }

    public function updatingSearch()
    {
        // La selección se conserva al buscar: permite marcar un envío, buscar otro
        // y entregarlos juntos. El contador del botón indica cuántos hay acumulados.
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingDevoluciones()
    {
        $this->seleccionados = [];
        $this->resetPage();
    }

    public function updatingOrigen()
    {
        $this->seleccionados = [];
        $this->resetPage();
    }

    /**
     * Devuelve solo las salas disponibles para la oficina del usuario logueado.
     * Si la sala tiene 'oficinas_con_sala' = null, está disponible para todos.
     * Si es un array, solo aparece si la oficina del usuario está en él.
     */
    public function getSalasPermitidasProperty(): array
    {
        $oficinaId = auth()->user()->oficina_id;

        return collect($this->salas_disponibles)
            ->filter(function ($sala) use ($oficinaId) {
                $oficinas = $sala['oficinas_con_sala'] ?? null;
                return is_null($oficinas) || in_array($oficinaId, $oficinas, true);
            })
            ->toArray();
    }

    /**
     * Entrega en lote: lleva los envíos seleccionados a la pantalla de Registro
     * de Entrega, donde se cobran con un solo pago (prorrateado por envío).
     * Traduce los envio_almacen_id seleccionados a envio_id, validando que sigan
     * activos en el almacén de esta oficina.
     */
    public function entregarSeleccionados()
    {
        if (empty($this->seleccionados)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        $envioIds = EnvioAlmacen::whereIn('envio_almacen_id', $this->seleccionados)
            ->where('oficina_id', auth()->user()->oficina_id)
            ->where('estatus', true)
            ->pluck('envio_id')
            ->unique()
            ->values();

        if ($envioIds->isEmpty()) {
            $this->dispatch('alertError', message: 'Los envíos seleccionados ya no están disponibles.');
            $this->seleccionados = [];
            return;
        }

        return $this->redirect(
            route('entrega-multiple', ['ids' => $envioIds->implode(',')]),
            navigate: true
        );
    }

    public function abrirModalSala()
    {
        if (empty($this->seleccionados)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        if (empty($this->salasPermitidas)) {
            $this->dispatch('alertError', message: 'Esta oficina no tiene salas configuradas para envío.');
            return;
        }

        $this->sala_destino = null;
        $this->mostrar_modal_sala = true;
    }

    /**
     * Agrega un envío al lote a partir del código escaneado/escrito.
     * El código debe pertenecer a un envío en el almacén activo de la oficina del usuario.
     */
    public function agregarPorCodigo()
    {
        $codigo = trim($this->codigo_lote);

        if ($codigo === '') {
            $this->mensaje_codigo = null;
            $this->mensaje_codigo_tipo = null;
            return;
        }

        $oficinaId = auth()->user()->oficina_id;

        $registro = EnvioAlmacen::whereHas('envio', fn($q) => $q->where('codigo_envio', $codigo))
            ->where('oficina_id', $oficinaId)
            ->where('estatus', true)
            ->first();

        if (!$registro) {
            $this->mensaje_codigo = 'Código no encontrado en el almacén de esta oficina.';
            $this->mensaje_codigo_tipo = 'error';
            $this->codigo_lote = '';
            return;
        }

        if (in_array($registro->envio_almacen_id, $this->seleccionados)) {
            $this->mensaje_codigo = 'Este envío ya está seleccionado.';
            $this->mensaje_codigo_tipo = 'warning';
            $this->codigo_lote = '';
            return;
        }

        $this->seleccionados[] = $registro->envio_almacen_id;
        $this->mensaje_codigo = "Envío {$codigo} agregado al lote.";
        $this->mensaje_codigo_tipo = 'success';
        $this->codigo_lote = '';
    }

    public function cerrarModalSala()
    {
        $this->mostrar_modal_sala = false;
        $this->sala_destino = null;
    }

    public function enviarASala()
    {
        if (empty($this->seleccionados)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        if (!$this->sala_destino || !isset($this->salas_disponibles[$this->sala_destino])) {
            $this->dispatch('alertError', message: 'Debes seleccionar una sala de destino.');
            return;
        }

        // Validar que la sala esté permitida para la oficina del usuario.
        $salasPermitidas = $this->salasPermitidas;
        if (!isset($salasPermitidas[$this->sala_destino])) {
            $this->dispatch('alertError', message: 'Esta sala no está disponible para tu oficina.');
            return;
        }

        $sala = $this->salas_disponibles[$this->sala_destino];
        $estatusId = $sala['estatus_id'];
        $esExpedicion = ($this->sala_destino === 'expedicion');

        // Expedición usa estatus dinámico (23 o 35 según servicio); otras salas requieren estatus fijo.
        if (!$esExpedicion && !$estatusId) {
            $this->dispatch('alertError', message: 'La sala seleccionada aún no está disponible.');
            return;
        }

        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        // Validar que los envíos seleccionados existan, sean de esta oficina y estén activos.
        $registros = EnvioAlmacen::with('envio')
            ->whereIn('envio_almacen_id', $this->seleccionados)
            ->where('oficina_id', $oficinaId)
            ->where('estatus', true)
            ->get();

        if ($registros->isEmpty()) {
            $this->dispatch('alertError', message: 'Los envíos seleccionados ya no están disponibles.');
            $this->seleccionados = [];
            $this->mostrar_modal_sala = false;
            return;
        }

        // Aduana solo acepta envíos internacionales.
        if ($this->sala_destino === 'aduana') {
            $envioIds = $registros->pluck('envio_id')->all();
            $hayNoInternacionales = Envio::whereIn('envio_id', $envioIds)
                ->where(function ($q) {
                    $q->where('tipo_envio', '!=', 'internacional')
                        ->orWhereNull('tipo_envio');
                })
                ->exists();

            if ($hayNoInternacionales) {
                $this->dispatch('alertError', message: 'Aduana solo acepta envíos internacionales. Revisa la selección y vuelve a intentarlo.');
                return;
            }
        }

        // Cualquier envío puede ir a cualquier sala; la única restricción por tipo de
        // envío es la de Aduana (solo internacionales), validada arriba. Antes había dos
        // reglas más por servicio —Distribución solo aceptaba SPU y Expedición lo
        // rechazaba— que se retiraron por decisión de negocio.

        DB::transaction(function () use ($registros, $estatusId, $usuario, $oficinaId, $sala, $esExpedicion) {
            foreach ($registros as $registro) {
                // Si la sala es Expedición, decidir el estatus según el servicio del envío.
                $estatusFinal = $estatusId;
                if ($esExpedicion) {
                    $servicioId = (int) ($registro->envio->servicio_id ?? 0);
                    $estatusFinal = match ($servicioId) {
                        self::SERVICIO_BULTO    => self::ESTATUS_SALIDA_EXPEDICION_BULTO,
                        self::SERVICIO_EMS      => self::ESTATUS_SALIDA_EXPEDICION_EMS,
                        self::SERVICIO_IPOSPLUS => self::ESTATUS_SALIDA_EXPEDICION_IPOSPLUS,
                        default                 => self::ESTATUS_SALIDA_EXPEDICION_GENERICO,
                    };
                }

                EnvioEncaminamiento::create([
                    'envio_id'           => $registro->envio_id,
                    'oficina_id'         => $oficinaId,
                    'oficina_externa_id' => null,
                    'usuario_id'         => $usuario->id,
                    'viaje_id'           => null,
                    'estatus_id'         => $estatusFinal,
                    'devolucion'         => false,
                ]);

                $registro->update([
                    'estatus' => false,
                    'Salida'  => now()->toDateTimeString(),
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'update',
                'descripcion' => 'Envió ' . $registros->count() . ' envío(s) desde Almacén a ' . $sala['nombre'],
            ]);
        });

        $cantidad = $registros->count();
        $this->seleccionados = [];
        $this->sala_destino = null;
        $this->mostrar_modal_sala = false;

        $this->dispatch('alertSuccess', message: "Se enviaron {$cantidad} envío(s) a {$sala['nombre']}.");
    }

    // ─────────── BANDEJA DE ACEPTACIÓN (Análisis → Apertura) ───────────

    /**
     * Query base de envíos pendientes de aceptación en Apertura:
     * - Último encaminamiento = 38 hacia mi oficina.
     * - Internacionales únicamente.
     * - Sin fila activa en envios_almacen.
     */
    protected function queryPendientesApertura()
    {
        $oficinaId = auth()->user()->oficina_id;

        return Envio::query()
            ->where('envios.tipo_envio', 'internacional')
            ->whereIn('envios.envio_id', function ($sub) use ($oficinaId) {
                $sub->select('e1.envio_id')
                    ->from('envios_encaminamiento as e1')
                    ->whereRaw('e1.envios_encaminamiento_id = (
                        SELECT MAX(e2.envios_encaminamiento_id)
                        FROM envios_encaminamiento e2
                        WHERE e2.envio_id = e1.envio_id
                    )')
                    ->where('e1.estatus_id', self::ESTATUS_SALIDA_HACIA_APERTURA)
                    ->where('e1.oficina_id', $oficinaId);
            })
            ->whereDoesntHave('almacen_actual');
    }

    public function abrirModalApertura()
    {
        $this->seleccionados_apertura = [];
        $this->mostrar_modal_apertura = true;
    }

    public function cerrarModalApertura()
    {
        $this->mostrar_modal_apertura = false;
        $this->seleccionados_apertura = [];
    }

    public function aceptarApertura()
    {
        if (empty($this->seleccionados_apertura)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        // Revalidar contra BD usando la misma query base.
        $enviosValidos = $this->queryPendientesApertura()
            ->whereIn('envios.envio_id', $this->seleccionados_apertura)
            ->pluck('envios.envio_id')
            ->toArray();

        if (empty($enviosValidos)) {
            $this->dispatch('alertError', message: 'Los envíos seleccionados ya no están disponibles.');
            $this->seleccionados_apertura = [];
            $this->mostrar_modal_apertura = false;
            return;
        }

        DB::transaction(function () use ($enviosValidos, $usuario, $oficinaId) {
            foreach ($enviosValidos as $envioId) {
                EnvioEncaminamiento::create([
                    'envio_id'           => $envioId,
                    'oficina_id'         => $oficinaId,
                    'oficina_externa_id' => null,
                    'usuario_id'         => $usuario->id,
                    'viaje_id'           => null,
                    'estatus_id'         => self::ESTATUS_ENTRADA_APERTURA,
                    'devolucion'         => false,
                ]);

                EnvioAlmacen::create([
                    'oficina_id' => $oficinaId,
                    'envio_id'   => $envioId,
                    'estatus'    => true,
                    'Entrada'    => now()->toDateTimeString(),
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'create',
                'descripcion' => 'Aceptó ' . count($enviosValidos) . ' envío(s) en Apertura',
            ]);
        });

        $cantidad = count($enviosValidos);
        $this->seleccionados_apertura = [];
        $this->mostrar_modal_apertura = false;

        $this->dispatch('alertSuccess', message: "Se aceptaron {$cantidad} envío(s) en Apertura.");
    }

    public function create(Envio $envio)
    {
        $this->resetValidation();
        $this->createForm->create($envio);
    }

    public function abrirModalAsignar($envioId)
    {
        $this->selected_envio = EnvioAlmacen::with(['carteros' => function ($query) {
            $query->wherePivot('estatus', true);
        }, 'envio.users'])->find($envioId);

        // Si hay carteros asignados, solo visualizar
        if ($this->selected_envio && $this->selected_envio->carteros->isNotEmpty()) {
            $this->cartero_seleccionado = $this->selected_envio->carteros->first()->id;
            $this->nombre_cartero = User::where('id', $this->cartero_seleccionado)->first()->name;
            $this->solo_visualizar = true;
        } else {

            $this->cartero_seleccionado = '';
            $this->nombre_cartero = '';
            $this->solo_visualizar = false;
        }

        $this->mostrar_modal_asignar = true;
    }

    public function asignarCartero()
    {
        $this->validate([
            'cartero_seleccionado' => 'required|exists:users,id',
        ]);

        if (!$this->selected_envio) {
            $this->dispatch('alertError', message: 'No se ha seleccionado un envío válido.');
            return;
        }

        $info = Envio::where('envio_id', $this->selected_envio->envio_id)->first();

        $asignacionExistente = DB::table('asignacion_envio_cartero')
            ->where('envio_almacen_id', $this->selected_envio->envio_almacen_id)

            ->where('user_id', $this->cartero_seleccionado)
            ->where('estatus', true)
            ->exists();

        if ($asignacionExistente) {
            $this->dispatch('alertInfo', message: 'El repartidor ya está asignado a este envío.');
            return;
        }

        DB::table('asignacion_envio_cartero')
            ->where('envio_almacen_id', $this->selected_envio->envio_almacen_id)
            ->where('estatus', true)
            ->update(['estatus' => false]);

        $this->selected_envio->carteros()->syncWithoutDetaching([
            $this->cartero_seleccionado => ['estatus' => true]
        ]);

        $Entrega = EnvioEncaminamiento::create([
            'usuario_id' => $this->usuario->id,
            'envio_id' => $this->selected_envio->envio_id,
            'oficina_id' => $this->usuario->oficina_id,

            'estatus_id' => 16,
            'devolucion' => $info->devolucion,
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se asignó el repartidor ({$this->cartero_seleccionado}) al envío ({$this->selected_envio->envio_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Repartidor asignado exitosamente.');

        $this->cerrarModalAsignar();
    }

    public function cerrarModalAsignar()
    {
        $this->mostrar_modal_asignar = false;
        $this->cartero_seleccionado = '';
        $this->selected_envio = null;
    }


    public function mostrar($envio)
    {
        $this->registro = EnvioAlmacen::find($envio);
        $this->cantidad_avisos = AlmacenAviso::where('envio_almacen_id', $this->registro['envio_almacen_id'])->count();
        $this->mostrar_modal = true;
    }

    public function modal_intento_entrega($envio)
    {
        $this->registro = EnvioAlmacen::find($envio);
        $this->cantidad_intentos = IntentoEntrega::where('envio_almacen_id', $this->registro['envio_almacen_id'])->count();
        $this->intento = true;
    }

    public function cerrar()
    {
        $this->registro = [];
        $this->mostrar_modal = false;
        $this->intento = false;
    }

    public function avisos()
    {
        DB::beginTransaction();

        try {
            $cant = AlmacenAviso::where('envio_almacen_id', $this->registro['envio_almacen_id'])->count();

            if ($cant < 8) {
                AlmacenAviso::create([
                    'envio_almacen_id' => $this->registro['envio_almacen_id'],
                    'envio_id' => $this->registro['envio_id'],
                    'usuario_id' => $this->usuario['id'],
                ]);

                DB::commit();
                $this->dispatch('alertSuccess', message: 'Aviso Registrado!');
                $this->cerrar();
            } else {
                $this->dispatch('alertSuccess2', message: 'Se ha alcanzado el máximo de avisos de llegada para este envío!');
                return;
            }
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Error al registrar el aviso!');
        }
    }

    public function intento_entrega()
    {
        DB::beginTransaction();

        try {
            $cant = IntentoEntrega::where('envio_almacen_id', $this->registro['envio_almacen_id'])->count();

            if ($cant < 3) {
                IntentoEntrega::create([
                    'envio_almacen_id' => $this->registro['envio_almacen_id'],
                    'envio_id' => $this->registro['envio_id'],
                    'usuario_id' => $this->usuario['id'],
                ]);

                DB::commit();
                $this->dispatch('alertSuccess', message: 'Intento de Entrega Registrado!');
                $this->cerrar();
            } else {
                $this->dispatch('alertSuccess2', message: 'Se ha alcanzado el máximo de intentos de entrega de este envío!');
                return;
            }
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('alertSuccess2', message: 'Error al registrar el intento de entrega!');
        }
    }



    public function store()
    {
        $this->createForm->store();
        $this->dispatch('alertSuccess', message: 'Entrega registrada Exitosamente');
    }

    public function generateEnviosPDF()
    {
        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        $envios = EnvioAlmacen::query()
            ->join('envios', 'envios_almacen.envio_id', '=', 'envios.envio_id')
            ->where('envios_almacen.estatus', $this->filterStatus)
            ->where('envios_almacen.oficina_id', $oficinaId)
            ->whereNotIn('envios_almacen.envio_id', function ($sub) {
                $sub->select('envio_id')
                    ->from('envios_encaminamiento as ee')
                    ->whereRaw('ee.envios_encaminamiento_id = (
                        select max(ee2.envios_encaminamiento_id)
                        from envios_encaminamiento as ee2
                        where ee2.envio_id = ee.envio_id
                    )')
                    ->where('ee.estatus_id', 1);
            })
            ->when($this->search, fn($query) =>
            $query->where('envios.codigo_envio', 'like', '%' . $this->search . '%'))
            ->when($this->origen == 1, fn($query) =>
            $query->where('envios.oficina_id', $oficinaId))
            ->when($this->origen == 2, fn($query) =>
            $query->where('envios.oficina_id', '!=', $oficinaId))
            ->select('envios_almacen.*', 'envios.*')
            ->orderBy($this->sortBy, $this->sortDir)
            ->get();

        $nombreOficina = optional($usuario->oficina)->nombre;

        // TITULO SEGÚN ESTATUS
        $tituloReporte = $this->filterStatus
            ? "Envíos disponibles en la oficina $nombreOficina"
            : "Envíos que salieron de la oficina $nombreOficina";

        $pdf = Pdf::loadView('pdf.envios_report', [
            'envios' => $envios,
            'nombreOficinaUsuario' => $nombreOficina,
            'usuario' => $usuario,
            'tituloReporte' => $tituloReporte,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_envios_almacen.pdf');
    }


    public function render()
    {
        $user = auth()->user();
        $oficina_id = auth()->user()->oficina_id;
        $tipoOficinaId = optional(auth()->user()->oficina->tipo_oficina)->tipo_oficina_id;
        $tipopagos = TipoPago::where('activo', true)->get();

        $envios = EnvioAlmacen::with([
            'carteros' => fn($q) => $q->wherePivot('estatus', true),
            'envio.users',
            'envio.encaminamiento_actual'
        ])
            ->where('estatus', $this->filterStatus)
            ->where('oficina_id', $oficina_id)
            ->whereDoesntHave('envio.encaminamiento_actual', fn($q) => $q->where('estatus_id', 1))

            // Condiciones para ver solo lo que le corresponde a cada rol
            ->when($user->hasRole('Clasificador EMS'), function ($query) {
                $query->whereHas('envio', function ($q) {
                    $q->where('servicio_id', 21);
                });
            })
            ->when($user->hasRole('Clasificador Bultos'), function ($query) {
                $query->whereHas('envio', function ($q) {
                    $q->where('servicio_id', 23);
                });
            })

            ->when($this->search, function ($query) {
                $query->whereHas('envio', function ($q) {
                    $q->where('codigo_envio', 'like', '%' . $this->search . '%');
                });
            })

            ->when($this->devoluciones == 1, function ($query) {
                $query->whereHas('envio', function ($q) {
                    $q->where('devolucion', true);
                });
            })

            // Origen del envío: creados en esta oficina vs recibidos desde otra OPT
            ->when($this->origen == 1, function ($query) use ($oficina_id) {
                $query->whereHas('envio', function ($q) use ($oficina_id) {
                    $q->where('oficina_id', $oficina_id);
                });
            })
            ->when($this->origen == 2, function ($query) use ($oficina_id) {
                $query->whereHas('envio', function ($q) use ($oficina_id) {
                    $q->where('oficina_id', '!=', $oficina_id);
                });
            })

            ->orderBy('created_at', 'DESC')
            ->paginate($this->perPage);

        // Bandeja de aceptación de Apertura (Análisis → Almacén).
        $pendientesAperturaCount = $this->queryPendientesApertura()->count();
        $pendientesApertura = $this->mostrar_modal_apertura
            ? $this->queryPendientesApertura()
                ->with(['servicio', 'users'])
                ->orderBy('envios.created_at', 'DESC')
                ->get()
            : collect();

        return view('livewire.oficinas.almacen', compact(
            'envios',
            'tipoOficinaId',
            'tipopagos',
            'pendientesAperturaCount',
            'pendientesApertura'
        ));
    }


    public function exportToExcel()
    {
        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;
        $nombreOficina = optional($usuario->oficina)->nombre;

        $envios = EnvioAlmacen::query()
            ->join('envios', 'envios_almacen.envio_id', '=', 'envios.envio_id')
            ->where('envios_almacen.estatus', $this->filterStatus)
            ->where('envios_almacen.oficina_id', $oficinaId)
            ->whereNotIn('envios_almacen.envio_id', function ($sub) {
                $sub->select('envio_id')
                    ->from('envios_encaminamiento as ee')
                    ->whereRaw('ee.envios_encaminamiento_id = (
                        select max(ee2.envios_encaminamiento_id)
                        from envios_encaminamiento as ee2
                        where ee2.envio_id = ee.envio_id
                    )')
                    ->where('ee.estatus_id', 1);
            })
            ->when($this->search, fn($query) =>
            $query->where('envios.codigo_envio', 'like', '%' . $this->search . '%'))
            ->when($this->origen == 1, fn($query) =>
            $query->where('envios.oficina_id', $oficinaId))
            ->when($this->origen == 2, fn($query) =>
            $query->where('envios.oficina_id', '!=', $oficinaId))
            ->select('envios_almacen.*', 'envios.*')
            ->orderBy($this->sortBy, $this->sortDir)
            ->get();

        $titulo = $this->filterStatus
            ? "Envíos disponibles en la oficina {$nombreOficina}"
            : "Envíos que salieron en la oficina {$nombreOficina}";



        return Excel::download(new EnviosExport($envios, $usuario, $nombreOficina, $titulo), 'envios.xlsx');
    }
}
