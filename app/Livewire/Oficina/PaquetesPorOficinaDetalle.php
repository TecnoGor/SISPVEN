<?php

namespace App\Livewire\Oficina;

use App\Models\Envio;
use App\Models\Oficina;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PaquetesPorOficinaDetalle extends Component
{
    use WithPagination;

    public Oficina $oficina;
    public $search = '';
    public $fecha_inicio;
    public $fecha_fin;
    public $perPage = 20;

    // IDs fijos del seeder: 3 = Llegada desde OPT, 4 = Llegada desde COP
    private const ESTATUS_ENTRADA_IDS = [3, 4];

    public function mount(int $oficina_id)
    {
        $this->oficina = Oficina::with('estado')->findOrFail($oficina_id);
        // Solo respeta query string si viene explícito (al venir desde Paquetes por Oficina).
        // Si se entra directo a la URL sin query, cae al día actual del usuario.
        $hoy = now()->toDateString();
        $this->fecha_inicio = request()->has('fecha_inicio') ? request('fecha_inicio') : $hoy;
        $this->fecha_fin = request()->has('fecha_fin') ? request('fecha_fin') : $hoy;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFechaInicio()
    {
        $this->resetPage();
    }

    public function updatingFechaFin()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    private function rangoFechas(): array
    {
        $hoy = now()->toDateString();
        $inicio = $this->fecha_inicio ?: $hoy;
        $fin = $this->fecha_fin ?: $hoy;
        if ($inicio > $fin) {
            [$inicio, $fin] = [$fin, $inicio];
        }
        return [$inicio, $fin];
    }

    public function render()
    {
        [$inicio, $fin] = $this->rangoFechas();

        $envios = Envio::with(['servicio', 'estadoDestino', 'oficinaOrigen.estado'])
            ->select('envios.*')
            ->selectSub(function ($query) use ($inicio, $fin) {
                $query->from('envios_encaminamiento as ee')
                    ->selectRaw('MIN(ee.created_at)')
                    ->whereColumn('ee.envio_id', 'envios.envio_id')
                    ->where('ee.oficina_id', $this->oficina->oficina_id)
                    ->whereIn('ee.estatus_id', self::ESTATUS_ENTRADA_IDS)
                    ->whereBetween('ee.created_at', [$inicio . ' 00:00:00', $fin . ' 23:59:59']);
            }, 'fecha_entrada_oficina')
            ->whereExists(function ($query) use ($inicio, $fin) {
                $query->select(DB::raw(1))
                    ->from('envios_encaminamiento as ee')
                    ->whereColumn('ee.envio_id', 'envios.envio_id')
                    ->where('ee.oficina_id', $this->oficina->oficina_id)
                    ->whereIn('ee.estatus_id', self::ESTATUS_ENTRADA_IDS)
                    ->whereBetween('ee.created_at', [$inicio . ' 00:00:00', $fin . ' 23:59:59']);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo_envio', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('nombre_dest', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('apellido_dest', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('documento_dest', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('envio_id')
            ->paginate($this->perPage);

        return view('livewire.oficina.paquetes-por-oficina-detalle', [
            'envios' => $envios,
            'fechaInicio' => $inicio,
            'fechaFin' => $fin,
        ]);
    }
}
