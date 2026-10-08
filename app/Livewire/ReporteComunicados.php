<?php

namespace App\Livewire;

use App\Models\Comunicado;
use App\Models\ComunicadoDestinatario;
use App\Models\EstatusComunicacion;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ReporteComunicados extends Component
{
    use WithPagination;

    public $search = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $tipo = '';
    public $estatus_id = '';
    public $perPage = 20;

    protected $queryString = ['search', 'fecha_inicio', 'fecha_fin', 'tipo', 'estatus_id'];

    public function mount()
    {
    }

    public function updating($name)
    {
        if (in_array($name, ['search', 'fecha_inicio', 'fecha_fin', 'tipo', 'estatus_id'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'tipo', 'estatus_id', 'fecha_inicio', 'fecha_fin']);
        $this->resetPage();
    }

    protected function baseQuery()
    {
        $query = Comunicado::with(['remitente', 'adjuntos'])
            ->select('comunicados.*');

        if ($this->fecha_inicio) {
            $query->whereDate('comunicados.created_at', '>=', $this->fecha_inicio);
        }
        if ($this->fecha_fin) {
            $query->whereDate('comunicados.created_at', '<=', $this->fecha_fin);
        }
        if ($this->tipo) {
            $query->where('tipo', $this->tipo);
        }
        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'ILIKE', $s)
                  ->orWhere('asunto', 'ILIKE', $s);
            });
        }

        return $query->orderByDesc('created_at');
    }

    protected function buildRows($comunicados)
    {
        $ids = $comunicados->pluck('comunicado_id')->all();

        $ultimosPivot = collect();
        $destFinales = collect();

        if (!empty($ids)) {
            $ultimoIds = ComunicadoDestinatario::select(DB::raw('MAX(id) as id'))
                ->whereIn('comunicado_id', $ids)
                ->groupBy('comunicado_id')
                ->pluck('id');

            $ultimosPivot = ComunicadoDestinatario::with(['usuario', 'estatus'])
                ->whereIn('id', $ultimoIds)
                ->get()
                ->keyBy('comunicado_id');

            $finalIds = $comunicados
                ->map(fn($c) => data_get($c->datos_json, 'destinatario_final_id'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (!empty($finalIds)) {
                $destFinales = User::whereIn('id', $finalIds)->get()->keyBy('id');
            }
        }

        $rows = $comunicados->map(function ($c) use ($ultimosPivot, $destFinales) {
            $ultimo = $ultimosPivot->get($c->comunicado_id);

            $tieneAhora = $ultimo && $ultimo->usuario ? $ultimo->usuario->name : '—';
            $estatus = $ultimo && $ultimo->estatus ? $ultimo->estatus->nombre : 'Pendiente';
            $estatus_id = $ultimo ? $ultimo->estatus_id : 1;
            $color = $ultimo && $ultimo->estatus ? $ultimo->estatus->color_badge : 'bg-red-100 text-red-800 border-red-200';

            $finalId = data_get($c->datos_json, 'destinatario_final_id');
            $destFinalUser = $finalId ? $destFinales->get($finalId) : null;
            $destFinal = $destFinalUser
                ? $destFinalUser->name
                : (data_get($c->datos_json, 'destinatario') ?: '—');

            $diasTranscurridos = (int) Carbon::parse($c->created_at)->startOfDay()->diffInDays(Carbon::now()->startOfDay());

            $vencido = false;
            $proximoVencer = false;
            if ($c->fecha_limite) {
                $limite = Carbon::parse($c->fecha_limite);
                $now = Carbon::now();
                $estadosCerrados = [4, 6, 8];
                $abierto = !in_array($estatus_id, $estadosCerrados);
                if ($abierto) {
                    if ($limite->isPast()) {
                        $vencido = true;
                    } elseif ($limite->diffInHours($now) <= 24) {
                        $proximoVencer = true;
                    }
                }
            }

            return (object) [
                'comunicado' => $c,
                'codigo' => $c->codigo,
                'tipo' => $c->tipo,
                'asunto' => $c->asunto,
                'prioridad' => $c->prioridad,
                'creado_por' => $c->remitente?->name ?? '—',
                'destinatario_final' => $destFinal,
                'tiene_ahora' => $tieneAhora,
                'estatus' => $estatus,
                'estatus_id' => $estatus_id,
                'estatus_color' => $color,
                'fecha_creacion' => Carbon::parse($c->created_at)->format('d/m/Y'),
                'fecha_limite' => $c->fecha_limite ? Carbon::parse($c->fecha_limite)->format('d/m/Y') : '—',
                'dias_transcurridos' => $diasTranscurridos,
                'vencido' => $vencido,
                'proximo_vencer' => $proximoVencer,
            ];
        });

        if ($this->estatus_id) {
            $rows = $rows->filter(fn($r) => (int) $r->estatus_id === (int) $this->estatus_id)->values();
        }

        return $rows;
    }

    public function render()
    {
        $comunicados = $this->baseQuery()->paginate($this->perPage);
        $rows = $this->buildRows(collect($comunicados->items()));

        $tipos = Comunicado::select('tipo')->distinct()->orderBy('tipo')->pluck('tipo');
        $estatus = EstatusComunicacion::orderBy('id')->get();

        return view('livewire.reporte-comunicados', [
            'rows' => $rows,
            'paginator' => $comunicados,
            'tipos' => $tipos,
            'estatus' => $estatus,
        ]);
    }

    public function exportarPdf()
    {
        abort_unless(\Illuminate\Support\Facades\Gate::allows('Ver Reportes'), 403);

        $comunicados = $this->baseQuery()->get();
        $rows = $this->buildRows($comunicados);

        $estatus = EstatusComunicacion::orderBy('id')->get();

        $estatus_nombre = null;
        if ($this->estatus_id) {
            $estatus_nombre = optional($estatus->firstWhere('id', (int) $this->estatus_id))->nombre;
        }

        $pdf = Pdf::loadView('pdf.reporte-comunicados', [
            'rows' => $rows,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'tipo' => $this->tipo,
            'estatus_nombre' => $estatus_nombre,
            'search' => $this->search,
            'generado_por' => auth()->user()->name,
            'generado_en' => Carbon::now()->format('d/m/Y h:i A'),
        ])->setPaper('a4', 'landscape');

        $filename = 'Reporte_Comunicados_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }
}
