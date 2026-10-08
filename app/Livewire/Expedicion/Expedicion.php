<?php

namespace App\Livewire\Expedicion;

use App\Models\Envio;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioExpedicion;
use App\Models\Servicio;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Expedicion extends Component
{
    use WithPagination;

    // Estatus paralelos según tipo de servicio.
    // Bulto/EMS/Iposplus tienen sus propios estatus; el resto cae en el genérico (41/42).
    const ESTATUS_SALIDA_EXPEDICION_BULTO     = 23;
    const ESTATUS_ENTRADA_EXPEDICION_BULTO    = 24;
    const ESTATUS_SALIDA_EXPEDICION_EMS       = 35;
    const ESTATUS_ENTRADA_EXPEDICION_EMS      = 36;
    const ESTATUS_SALIDA_EXPEDICION_IPOSPLUS  = 39;
    const ESTATUS_ENTRADA_EXPEDICION_IPOSPLUS = 40;
    const ESTATUS_SALIDA_EXPEDICION_GENERICO  = 41;
    const ESTATUS_ENTRADA_EXPEDICION_GENERICO = 42;

    // Servicios involucrados.
    const SERVICIO_BULTO    = 23;
    const SERVICIO_EMS      = 21;
    const SERVICIO_IPOSPLUS = 10;

    public $search = '';
    public $servicio_id = '';
    public $desde = '';
    public $hasta = '';
    public $perPage = 15;

    // 'por_aceptar' | 'en_expedicion'
    public string $vista = 'por_aceptar';

    // Selección múltiple para aceptación en lote
    public array $seleccionados_aceptar = []; // envio_id

    public function updatedSearch()     { $this->resetPage(); }
    public function updatedServicioId() { $this->resetPage(); }
    public function updatedDesde()      { $this->resetPage(); }
    public function updatedHasta()      { $this->resetPage(); }
    public function updatedPerPage()    { $this->resetPage(); }

    public function setVista(string $vista)
    {
        if (!in_array($vista, ['por_aceptar', 'en_expedicion'])) {
            return;
        }
        $this->vista = $vista;
        $this->seleccionados_aceptar = [];
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'servicio_id', 'desde', 'hasta']);
        $this->resetPage();
    }

    /**
     * Mapeo dinámico de estatus de aceptación según el servicio del envío.
     */
    protected function estatusEntradaPorServicio(int $servicioId): int
    {
        return match ($servicioId) {
            self::SERVICIO_BULTO    => self::ESTATUS_ENTRADA_EXPEDICION_BULTO,
            self::SERVICIO_EMS      => self::ESTATUS_ENTRADA_EXPEDICION_EMS,
            self::SERVICIO_IPOSPLUS => self::ESTATUS_ENTRADA_EXPEDICION_IPOSPLUS,
            default                 => self::ESTATUS_ENTRADA_EXPEDICION_GENERICO,
        };
    }

    // ─────────── ACEPTACIÓN (POR ACEPTAR → EN EXPEDICIÓN) ───────────

    public function aceptarSeleccionados()
    {
        if (empty($this->seleccionados_aceptar)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        // Releer desde BD respetando filtros y rol.
        $enviosValidos = $this->queryPorAceptar($oficinaId)
            ->whereIn('envios.envio_id', $this->seleccionados_aceptar)
            ->get(['envios.envio_id', 'envios.servicio_id']);

        if ($enviosValidos->isEmpty()) {
            $this->dispatch('alertError', message: 'Los envíos seleccionados ya no están disponibles.');
            $this->seleccionados_aceptar = [];
            return;
        }

        DB::transaction(function () use ($enviosValidos, $oficinaId, $usuario) {
            foreach ($enviosValidos as $envio) {
                $estatusId = $this->estatusEntradaPorServicio((int) $envio->servicio_id);

                EnvioEncaminamiento::create([
                    'envio_id'           => $envio->envio_id,
                    'oficina_id'         => $oficinaId,
                    'oficina_externa_id' => null,
                    'usuario_id'         => $usuario->id,
                    'viaje_id'           => null,
                    'estatus_id'         => $estatusId,
                    'devolucion'         => false,
                ]);

                EnvioExpedicion::create([
                    'oficina_id'         => $oficinaId,
                    'envio_id'           => $envio->envio_id,
                    'estatus'            => true,
                    'Entrada'            => now()->toDateString(),
                    'usuario_ingreso_id' => $usuario->id,
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'create',
                'descripcion' => 'Aceptó ' . $enviosValidos->count() . ' envío(s) en Expedición',
            ]);
        });

        $cantidad = $enviosValidos->count();
        $this->seleccionados_aceptar = [];

        $this->dispatch('alertSuccess', message: "Se aceptaron {$cantidad} envío(s) en Expedición.");
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
     * marca exactamente lo que el operador está viendo (incluido el filtro por rol
     * que aplica queryPorAceptar).
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

    /**
     * Envíos cuyo último encaminamiento es "Salida hacia Expedición" (Bulto o EMS) hacia mi oficina,
     * filtrados por rol del usuario, y sin fila activa en envios_expedicion.
     */
    protected function queryPorAceptar(int $oficinaId)
    {
        $user = auth()->user();

        return Envio::query()
            ->whereIn('envios.envio_id', function ($sub) use ($oficinaId) {
                $sub->select('e1.envio_id')
                    ->from('envios_encaminamiento as e1')
                    ->whereRaw('e1.envios_encaminamiento_id = (
                        SELECT MAX(e2.envios_encaminamiento_id)
                        FROM envios_encaminamiento e2
                        WHERE e2.envio_id = e1.envio_id
                    )')
                    ->whereIn('e1.estatus_id', [
                        self::ESTATUS_SALIDA_EXPEDICION_BULTO,
                        self::ESTATUS_SALIDA_EXPEDICION_EMS,
                        self::ESTATUS_SALIDA_EXPEDICION_IPOSPLUS,
                        self::ESTATUS_SALIDA_EXPEDICION_GENERICO,
                    ])
                    ->where('e1.oficina_id', $oficinaId);
            })
            ->whereDoesntHave('expedicion_actual')
            ->when($user->hasRole('Clasificador EMS'), function ($q) {
                $q->where('envios.servicio_id', self::SERVICIO_EMS);
            })
            ->when($user->hasRole('Clasificador Bultos'), function ($q) {
                $q->where('envios.servicio_id', self::SERVICIO_BULTO);
            });
    }

    public function render()
    {
        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        if ($this->vista === 'por_aceptar') {
            $registros = $this->registrosPorAceptar();
        } else { // en_expedicion
            $registros = EnvioExpedicion::with([
                    'envio.servicio',
                    'envio.oficinas',
                    'envio.sacas' => fn($q) => $q->wherePivot('activo', true),
                    'usuarioIngreso',
                ])
                ->where('estatus', true)
                ->where('oficina_id', $oficinaId)
                ->when($usuario->hasRole('Clasificador EMS'), function ($q) {
                    $q->whereHas('envio', fn($q2) => $q2->where('servicio_id', self::SERVICIO_EMS));
                })
                ->when($usuario->hasRole('Clasificador Bultos'), function ($q) {
                    $q->whereHas('envio', fn($q2) => $q2->where('servicio_id', self::SERVICIO_BULTO));
                })
                ->when($this->servicio_id, function ($q) {
                    $q->whereHas('envio', fn($q2) => $q2->where('servicio_id', $this->servicio_id));
                })
                ->when($this->desde, fn($q) => $q->whereDate('Entrada', '>=', $this->desde))
                ->when($this->hasta, fn($q) => $q->whereDate('Entrada', '<=', $this->hasta))
                ->when($this->search, function ($q) {
                    $q->whereHas('envio', function ($q2) {
                        $q2->where('codigo_envio', 'like', '%' . $this->search . '%')
                           ->orWhere('nombre_rem',  'like', '%' . $this->search . '%')
                           ->orWhere('nombre_dest', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('Entrada', 'desc')
                ->paginate($this->perPage);
        }

        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.expedicion.expedicion', [
            'registros' => $registros,
            'servicios' => $servicios,
        ]);
    }
}