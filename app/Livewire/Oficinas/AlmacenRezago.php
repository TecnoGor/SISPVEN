<?php

namespace App\Livewire\Oficinas;

use App\Models\Envio;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioRezago;
use App\Models\Servicio;
use App\Models\UsuarioSeguimiento;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AlmacenRezago extends Component
{
    use WithPagination;

    // Estatus involucrados en el flujo del almacén de rezago.
    const ESTATUS_SALIDA   = 25; // "Salida a Rezago"
    const ESTATUS_RECIBIDO = 26; // "Recibido en Rezago"

    public $usuario;
    public $search = '';
    public $perPage = 15;
    public $servicio_filtro = '';

    // 'por_aceptar' | 'en_rezago'
    public string $vista = 'por_aceptar';

    // Filtros del listado EN REZAGO
    public $desde = '';
    public $hasta = '';
    public $dias_minimo = '';

    // Selección múltiple para aceptación en lote
    public array $seleccionados_aceptar = [];

    public function mount()
    {
        $this->usuario = auth()->user();
    }

    public function updatingSearch()         { $this->resetPage(); }
    public function updatingPerPage()        { $this->resetPage(); }
    public function updatingServicioFiltro() { $this->resetPage(); }
    public function updatingDesde()          { $this->resetPage(); }
    public function updatingHasta()          { $this->resetPage(); }
    public function updatingDiasMinimo()     { $this->resetPage(); }

    public function setVista(string $vista)
    {
        if (!in_array($vista, ['por_aceptar', 'en_rezago'])) {
            return;
        }
        $this->vista = $vista;
        $this->seleccionados_aceptar = [];
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'servicio_filtro', 'desde', 'hasta', 'dias_minimo']);
        $this->resetPage();
    }

    // ─────────── ACEPTACIÓN (POR ACEPTAR → EN REZAGO) ───────────

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

                EnvioRezago::create([
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
                'descripcion' => 'Aceptó ' . count($enviosValidos) . ' envío(s) en el Almacén de Rezago',
            ]);
        });

        $cantidad = count($enviosValidos);
        $this->seleccionados_aceptar = [];

        $this->dispatch('alertSuccess', message: "Se aceptaron {$cantidad} envío(s) en rezago.");
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
            ->with(['servicio', 'users'])
            ->when($this->servicio_filtro, fn($q) => $q->where('servicio_id', $this->servicio_filtro))
            ->when($this->search, function ($q) {
                $q->where('codigo_envio', 'like', '%' . $this->search . '%');
            })
            ->orderBy('envios.created_at', 'DESC')
            ->paginate($this->perPage);
    }

    /**
     * Envíos cuyo último encaminamiento es "Salida a Rezago" hacia mi oficina,
     * y que aún no tienen una fila activa en envios_rezago.
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
                    ->where('e1.estatus_id', self::ESTATUS_SALIDA)
                    ->where('e1.oficina_id', $oficinaId);
            })
            ->whereDoesntHave('rezago_actual');
    }

    public function render()
    {
        $oficinaId = auth()->user()->oficina_id;

        if ($this->vista === 'por_aceptar') {
            $envios = $this->registrosPorAceptar();
        } else {
            $envios = EnvioRezago::with(['envio.servicio', 'envio.users', 'usuarioIngreso'])
                ->where('oficina_id', $oficinaId)
                ->where('estatus', true)
                ->when($this->desde, fn($q) => $q->whereDate('Entrada', '>=', $this->desde))
                ->when($this->hasta, fn($q) => $q->whereDate('Entrada', '<=', $this->hasta))
                ->when($this->dias_minimo !== '' && $this->dias_minimo !== null, function ($q) {
                    $q->whereDate('Entrada', '<=', Carbon::now()->subDays((int) $this->dias_minimo));
                })
                ->when($this->servicio_filtro, function ($q) {
                    $q->whereHas('envio', fn($q2) => $q2->where('servicio_id', $this->servicio_filtro));
                })
                ->when($this->search, function ($q) {
                    $q->whereHas('envio', fn($q2) => $q2->where('codigo_envio', 'like', '%' . $this->search . '%'));
                })
                ->orderBy('Entrada', 'DESC')
                ->paginate($this->perPage);
        }

        $servicios = Servicio::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.oficinas.almacen-rezago', compact('envios', 'servicios'));
    }

    // ─────────── REPORTES PDF ───────────

    /**
     * Reporte: envíos actualmente en el almacén de rezago.
     */
    public function reporteEstan()
    {
        $envios = EnvioRezago::with(['envio.users', 'envio.servicio'])
            ->where('oficina_id', auth()->user()->oficina_id)
            ->where('estatus', true)
            ->orderBy('Entrada', 'DESC')
            ->get();

        return $this->generarPDF($envios, 'Paquetes que están en el almacén de rezago', 'rezago_estan.pdf');
    }

    /**
     * Reporte: envíos que llevan más de N días en rezago.
     * Usa $this->dias_minimo si está definido, sino un valor por defecto.
     */
    public function reporteLlevanTiempo()
    {
        $dias = (int) ($this->dias_minimo ?: 60);

        $envios = EnvioRezago::with(['envio.users', 'envio.servicio'])
            ->where('oficina_id', auth()->user()->oficina_id)
            ->where('estatus', true)
            ->whereDate('Entrada', '<=', Carbon::now()->subDays($dias))
            ->orderBy('Entrada', 'ASC')
            ->get();

        return $this->generarPDF($envios, "Paquetes que llevan más de {$dias} días en rezago", 'rezago_llevan_tiempo.pdf');
    }

    protected function generarPDF($envios, $titulo, $nombreArchivo)
    {
        $usuario = auth()->user();

        $pdf = Pdf::loadView('pdf.rezago_report', [
            'envios' => $envios,
            'nombreOficinaUsuario' => optional($usuario->oficina)->nombre,
            'usuario' => $usuario,
            'tituloReporte' => $titulo,
        ]);

        return response()->streamDownload(fn() => print($pdf->stream()), $nombreArchivo);
    }
}