<?php

namespace App\Livewire\UnidadAnalisisDevolucion;

use App\Models\Envio;
use App\Models\EnvioEncaminamiento;
use App\Models\Servicio;
use App\Models\UnidadAnalisisDevolucion as ModelsUnidadAnalisisDevolucion;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UnidadAnalisisDevolucion extends Component
{
    use WithPagination;

    // Estatus involucrados en el flujo de la unidad.
    const ESTATUS_SALIDA   = 21; // "Salida a Unidad de Análisis"
    const ESTATUS_RECIBIDO = 22; // "Recibido en Unidad de Análisis"

    public $search = '';
    public $servicio_id = '';
    public $desde = '';
    public $hasta = '';
    public $perPage = 15;

    // 'por_aceptar' | 'en_analisis' | 'historico'
    public string $vista = 'por_aceptar';

    // Filtro adicional solo del histórico: por estatus_id de la decisión final.
    public $filtro_decision = '';

    // Selección múltiple — un array por sección para evitar mezclar IDs distintos.
    public array $seleccionados_aceptar  = []; // envio_id
    public array $seleccionados_despacho = []; // envio_unidad_analisis_devolucion_id

    // Modal de despacho a sala (solo aplica a la sección EN ANÁLISIS).
    public bool $mostrar_modal_despacho = false;
    public $sala_destino = null;
    public string $observaciones_decision = '';

    public array $salas_destino = [
        'apertura' => [
            'nombre'     => 'Apertura',
            'estatus_id' => 38,
        ],
        'expedicion' => [
            'nombre'     => 'Expedición',
            'estatus_id' => 23,
        ],
        'rezago' => [
            'nombre'     => 'Rezago',
            'estatus_id' => 25,
        ],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedServicioId()
    {
        $this->resetPage();
    }
    public function updatedDesde()
    {
        $this->resetPage();
    }
    public function updatedHasta()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    public function updatedFiltroDecision()
    {
        $this->resetPage();
    }

    public function setVista(string $vista)
    {
        if (!in_array($vista, ['por_aceptar', 'en_analisis', 'historico'])) {
            return;
        }
        $this->vista = $vista;
        $this->seleccionados_aceptar  = [];
        $this->seleccionados_despacho = [];
        $this->filtro_decision = '';
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'servicio_id', 'desde', 'hasta', 'filtro_decision']);
        $this->resetPage();
    }

    // ─────────── ACEPTACIÓN (POR ACEPTAR → EN ANÁLISIS) ───────────

    public function aceptarSeleccionados()
    {
        if (empty($this->seleccionados_aceptar)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        $enviosValidos = $this->queryPorAceptar($oficinaId)
            ->whereIn('envios.envio_id', $this->seleccionados_aceptar)
            ->pluck('envios.envio_id')
            ->toArray();

        if (empty($enviosValidos)) {
            $this->dispatch('alertError', message: 'Los envíos seleccionados ya no están disponibles.');
            $this->seleccionados_aceptar = [];
            return;
        }

        DB::transaction(function () use ($enviosValidos, $oficinaId, $usuario) {
            foreach ($enviosValidos as $envioId) {
                EnvioEncaminamiento::create([
                    'envio_id'           => $envioId,
                    'oficina_id'         => $oficinaId,
                    'oficina_externa_id' => null,
                    'usuario_id'         => $usuario->id,
                    'viaje_id'           => null,
                    'estatus_id'         => self::ESTATUS_RECIBIDO,
                    'devolucion'         => false,
                ]);

                ModelsUnidadAnalisisDevolucion::create([
                    'envio_id'            => $envioId,
                    'oficina_id'          => $oficinaId,
                    'usuario_ingreso_id'  => $usuario->id,
                    'estatus'             => true,
                    'fecha_ingreso'       => now(),
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'create',
                'descripcion' => 'Aceptó ' . count($enviosValidos) . ' envío(s) en la Unidad de Análisis de Devolución',
            ]);
        });

        $cantidad = count($enviosValidos);
        $this->seleccionados_aceptar = [];

        $this->dispatch('alertSuccess', message: "Se aceptaron {$cantidad} envío(s) en la unidad.");
    }

    // ─────────── DESPACHO (EN ANÁLISIS → otra sala) ───────────

    public function abrirModalDespacho()
    {
        if (empty($this->seleccionados_despacho)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }
        $this->sala_destino = null;
        $this->observaciones_decision = '';
        $this->mostrar_modal_despacho = true;
    }

    public function cerrarModalDespacho()
    {
        $this->mostrar_modal_despacho = false;
        $this->sala_destino = null;
        $this->observaciones_decision = '';
    }

    public function despacharASala()
    {
        if (empty($this->seleccionados_despacho)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        if (!$this->sala_destino || !isset($this->salas_destino[$this->sala_destino])) {
            $this->dispatch('alertError', message: 'Debes seleccionar una sala de destino.');
            return;
        }

        $sala = $this->salas_destino[$this->sala_destino];
        $estatusId = $sala['estatus_id'];

        if (!$estatusId) {
            $this->dispatch('alertError', message: 'La sala seleccionada aún no está disponible.');
            return;
        }

        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        $registros = ModelsUnidadAnalisisDevolucion::whereIn('envio_unidad_analisis_devolucion_id', $this->seleccionados_despacho)
            ->where('oficina_id', $oficinaId)
            ->where('estatus', true)
            ->get();

        if ($registros->isEmpty()) {
            $this->dispatch('alertError', message: 'Los envíos seleccionados ya no están disponibles.');
            $this->seleccionados_despacho = [];
            $this->mostrar_modal_despacho = false;
            return;
        }

        // Cuando se devuelve a Apertura, el envío queda en encaminamiento 38 (Salida hacia Apertura).
        // El operador de Almacén debe aceptarlo manualmente para que entre al almacén con estatus 37.
        DB::transaction(function () use ($registros, $estatusId, $usuario, $oficinaId, $sala) {
            foreach ($registros as $registro) {
                EnvioEncaminamiento::create([
                    'envio_id'           => $registro->envio_id,
                    'oficina_id'         => $oficinaId,
                    'oficina_externa_id' => null,
                    'usuario_id'         => $usuario->id,
                    'viaje_id'           => null,
                    'estatus_id'         => $estatusId,
                    'devolucion'         => false,
                ]);

                $registro->update([
                    'estatus'                => false,
                    'fecha_decision'         => now(),
                    'usuario_decision_id'    => $usuario->id,
                    'decision_final'         => $estatusId,
                    'observaciones_decision' => $this->observaciones_decision ?: null,
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'update',
                'descripcion' => 'Despachó ' . $registros->count() . ' envío(s) desde la Unidad de Análisis a ' . $sala['nombre'],
            ]);
        });

        $cantidad = $registros->count();
        $this->seleccionados_despacho = [];
        $this->sala_destino = null;
        $this->observaciones_decision = '';
        $this->mostrar_modal_despacho = false;

        $this->dispatch('alertSuccess', message: "Se despacharon {$cantidad} envío(s) a {$sala['nombre']}.");
    }

    /**
     * Marca o desmarca de golpe los envíos de la página actual.
     *
     * Solo la página visible: seleccionar todas las páginas dejaría aceptar envíos que
     * el operador nunca vio. Los ya seleccionados en otras páginas se conservan.
     */
    public function toggleSeleccionPagina()
    {
        // pluck sin prefijo de tabla: sobre la colección del paginador, "envios.envio_id"
        // se interpreta como acceso anidado y devuelve null.
        $idsPagina = $this->registrosPorAceptar()
            ->pluck('envio_id')
            ->map(fn($id) => (string) $id)
            ->all();

        $seleccionados = array_map('strval', $this->seleccionados_aceptar);

        // Si ya están todos los de esta página, el clic los quita; si no, los añade.
        if (!array_diff($idsPagina, $seleccionados)) {
            $this->seleccionados_aceptar = array_values(array_diff($seleccionados, $idsPagina));
            return;
        }

        $this->seleccionados_aceptar = array_values(array_unique(array_merge($seleccionados, $idsPagina)));
    }

    // ─────────── QUERIES ───────────

    /**
     * Página actual de "Por aceptar" con los filtros de pantalla ya aplicados.
     * La usan render() y toggleSeleccionPagina(), así el checkbox de la cabecera
     * marca exactamente lo que el operador está viendo.
     */
    protected function registrosPorAceptar()
    {
        return $this->queryPorAceptar(auth()->user()->oficina_id)
            ->with(['servicio', 'oficinas'])
            ->when($this->servicio_id, fn($q) => $q->where('servicio_id', $this->servicio_id))
            ->when($this->desde, fn($q) => $q->whereDate('envios.created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('envios.created_at', '<=', $this->hasta))
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('codigo_envio', 'like', '%' . $this->search . '%')
                        ->orWhere('nombre_rem',  'like', '%' . $this->search . '%')
                        ->orWhere('nombre_dest', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('envios.created_at', 'desc')
            ->paginate($this->perPage);
    }

    protected function queryPorAceptar(int $oficinaId)
    {
        return Envio::query()
            ->whereIn('envios.envio_id', function ($sub) use ($oficinaId) {
                $sub->select('e1.envio_id')
                    ->from('envios_encaminamiento as e1')
                    ->whereRaw('e1.envios_encaminamiento_id = (
                        SELECT MAX(e2.envios_encaminamiento_id)
                        FROM envios_encaminamiento e2
                        WHERE e2.envio_id = e1.envio_id
                    )')
                    ->where('e1.estatus_id', self::ESTATUS_SALIDA)
                    ->where('e1.oficina_id', $oficinaId);
            })
            ->whereDoesntHave('unidad_analisis_actual');
    }

    public function render()
    {
        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        if ($this->vista === 'por_aceptar') {
            $registros = $this->registrosPorAceptar();
        } elseif ($this->vista === 'en_analisis') {
            $registros = ModelsUnidadAnalisisDevolucion::with([
                'envio.servicio',
                'envio.oficinas',
                'usuarioIngreso',
            ])
                ->where('estatus', true)
                ->where('oficina_id', $oficinaId)
                ->when($this->servicio_id, function ($q) {
                    $q->whereHas('envio', fn($q2) => $q2->where('servicio_id', $this->servicio_id));
                })
                ->when($this->desde, fn($q) => $q->whereDate('fecha_ingreso', '>=', $this->desde))
                ->when($this->hasta, fn($q) => $q->whereDate('fecha_ingreso', '<=', $this->hasta))
                ->when($this->search, function ($q) {
                    $q->whereHas('envio', function ($q2) {
                        $q2->where('codigo_envio', 'like', '%' . $this->search . '%')
                            ->orWhere('nombre_rem',  'like', '%' . $this->search . '%')
                            ->orWhere('nombre_dest', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('fecha_ingreso', 'desc')
                ->paginate($this->perPage);
        } else { // historico
            $registros = ModelsUnidadAnalisisDevolucion::with([
                'envio.servicio',
                'envio.oficinas',
                'usuarioIngreso',
                'usuarioDecision',
                'envioEstatus',
            ])
                ->where('estatus', false)
                ->where('oficina_id', $oficinaId)
                ->when($this->servicio_id, function ($q) {
                    $q->whereHas('envio', fn($q2) => $q2->where('servicio_id', $this->servicio_id));
                })
                ->when($this->desde, fn($q) => $q->whereDate('fecha_decision', '>=', $this->desde))
                ->when($this->hasta, fn($q) => $q->whereDate('fecha_decision', '<=', $this->hasta))
                ->when($this->filtro_decision, fn($q) => $q->where('decision_final', $this->filtro_decision))
                ->when($this->search, function ($q) {
                    $q->whereHas('envio', function ($q2) {
                        $q2->where('codigo_envio', 'like', '%' . $this->search . '%')
                            ->orWhere('nombre_rem',  'like', '%' . $this->search . '%')
                            ->orWhere('nombre_dest', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('fecha_decision', 'desc')
                ->paginate($this->perPage);
        }

        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.unidad-analisis-devolucion.unidad-analisis-devolucion', [
            'registros' => $registros,
            'servicios' => $servicios,
        ]);
    }
}
