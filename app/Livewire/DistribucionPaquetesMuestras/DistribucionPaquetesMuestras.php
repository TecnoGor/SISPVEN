<?php

namespace App\Livewire\DistribucionPaquetesMuestras;

use App\Models\Envio;
use App\Models\EnvioDistribucionPaqueteMuestra;
use App\Models\EnvioEncaminamiento;
use App\Models\Servicio;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DistribucionPaquetesMuestras extends Component
{
    use WithPagination;

    // Estatus involucrados.
    const ESTATUS_SALIDA_DISTRIBUCION  = 19; // "SALIDA HACIA DISTRIBUCIÓN PAQUETES MUESTRAS"
    const ESTATUS_ENTRADA_DISTRIBUCION = 20; // "ENTRADA DISTRIBUCIÓN PAQUETES MUESTRAS"

    // Servicios permitidos (Servicio Postal Universal).
    const SERVICIO_SPU_NACIONAL      = 1;
    const SERVICIO_SPU_INTERNACIONAL = 14;
    const SERVICIO_SACAS_M           = 18;

    public $search = '';
    public $servicio_id = '';
    public $desde = '';
    public $hasta = '';
    public $perPage = 15;

    // 'por_aceptar' | 'en_distribucion'
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
        if (!in_array($vista, ['por_aceptar', 'en_distribucion'])) {
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

    // ─────────── ACEPTACIÓN (POR ACEPTAR → EN DISTRIBUCIÓN) ───────────

    public function aceptarSeleccionados()
    {
        if (empty($this->seleccionados_aceptar)) {
            $this->dispatch('alertError', message: 'No hay envíos seleccionados.');
            return;
        }

        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        // Releer desde BD aplicando los mismos filtros de la vista (defensa).
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
                    'estatus_id'         => self::ESTATUS_ENTRADA_DISTRIBUCION,
                    'devolucion'         => false,
                ]);

                EnvioDistribucionPaqueteMuestra::create([
                    'oficina_id'         => $oficinaId,
                    'envio_id'           => $envioId,
                    'estatus'            => true,
                    'Entrada'            => now()->toDateString(),
                    'usuario_ingreso_id' => $usuario->id,
                ]);
            }

            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'create',
                'descripcion' => 'Aceptó ' . count($enviosValidos) . ' envío(s) en Distribución Paquetes Muestras',
            ]);
        });

        $cantidad = count($enviosValidos);
        $this->seleccionados_aceptar = [];

        $this->dispatch('alertSuccess', message: "Se aceptaron {$cantidad} envío(s) en Distribución.");
    }

    // ─────────── QUERIES ───────────

    /**
     * Envíos cuyo último encaminamiento es "Salida hacia Distribución" hacia mi oficina
     * y que aún no tienen una fila activa en envios_distribucion_paquetes_muestras.
     *
     * Sin filtro por servicio: desde Almacén puede enviarse cualquier envío a esta sala,
     * así que restringir aquí a SPU dejaba invisibles los que sí salieron del almacén.
     */
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
                    ->where('e1.estatus_id', self::ESTATUS_SALIDA_DISTRIBUCION)
                    ->where('e1.oficina_id', $oficinaId);
            })
            ->whereDoesntHave('distribucion_actual');
    }

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

    public function render()
    {
        $usuario = auth()->user();
        $oficinaId = $usuario->oficina_id;

        if ($this->vista === 'por_aceptar') {
            $registros = $this->registrosPorAceptar();
        } else { // en_distribucion
            $registros = EnvioDistribucionPaqueteMuestra::with([
                    'envio.servicio',
                    'envio.oficinas',
                    'envio.sacas' => fn($q) => $q->wherePivot('activo', true),
                    'usuarioIngreso',
                ])
                ->where('estatus', true)
                ->where('oficina_id', $oficinaId)
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

        // Todos los servicios activos: a esta sala puede llegar cualquier envío, así que
        // el desplegable de filtro no debe limitarse a los del Servicio Postal Universal.
        $servicios = Servicio::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('livewire.distribucion-paquetes-muestras.distribucion-paquetes-muestras', [
            'registros' => $registros,
            'servicios' => $servicios,
        ]);
    }
}