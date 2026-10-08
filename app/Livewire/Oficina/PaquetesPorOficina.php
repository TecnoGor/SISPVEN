<?php

namespace App\Livewire\Oficina;

use App\Models\Estado;
use App\Models\Oficina;
use App\Models\TipoOficina;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PaquetesPorOficina extends Component
{
    use WithPagination;

    public $search = '';
    public $fecha_inicio;
    public $fecha_fin;
    public $tipo_oficina_id = '';
    public $estado_id = '';
    public $perPage = 20;

    // IDs fijos del seeder: 3 = Llegada desde OPT, 4 = Llegada desde COP
    private const ESTATUS_ENTRADA_IDS = [3, 4];

    // Tipos de oficina visibles: 1-3 = OPT (Pequeña, Mediana, Grande), 4 = COP
    private const TIPOS_OFICINA_VISIBLES = [1, 2, 3, 4];

    public function mount()
    {
        // SIEMPRE inicia con la fecha de hoy. El query string se ignora a propósito
        // para que cada vez que el usuario entra al módulo vea el día actual.
        $hoy = now()->toDateString();
        $this->fecha_inicio = $hoy;
        $this->fecha_fin = $hoy;
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

    public function updatingTipoOficinaId()
    {
        $this->resetPage();
    }

    public function updatingEstadoId()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Devuelve el rango normalizado [inicio, fin] aplicando defaults
     * y volteo si el usuario invirtió las fechas.
     */
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

        $oficinas = Oficina::with('estado')
            ->select('oficinas.*')
            ->whereIn('tipo_oficina_id', self::TIPOS_OFICINA_VISIBLES)
            ->selectSub(function ($query) use ($inicio, $fin) {
                $query->from('envios_encaminamiento as ee')
                    ->selectRaw('COUNT(DISTINCT ee.envio_id)')
                    ->whereColumn('ee.oficina_id', 'oficinas.oficina_id')
                    ->whereIn('ee.estatus_id', self::ESTATUS_ENTRADA_IDS)
                    ->whereBetween('ee.created_at', [$inicio . ' 00:00:00', $fin . ' 23:59:59']);
            }, 'total_envios_entrada')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('codigo', 'LIKE', '%' . $this->search . '%')
                      ->orWhereHas('estado', function ($qe) {
                          $qe->where('nombre', 'LIKE', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->tipo_oficina_id !== '' && $this->tipo_oficina_id !== null, function ($query) {
                $query->where('tipo_oficina_id', $this->tipo_oficina_id);
            })
            ->when($this->estado_id !== '' && $this->estado_id !== null, function ($query) {
                $query->where('estado_id', $this->estado_id);
            })
            ->orderBy('nombre')
            ->paginate($this->perPage);

        return view('livewire.oficina.paquetes-por-oficina', [
            'oficinas' => $oficinas,
            'fechaInicio' => $inicio,
            'fechaFin' => $fin,
            'tiposOficina' => TipoOficina::whereIn('tipo_oficina_id', self::TIPOS_OFICINA_VISIBLES)
                ->orderBy('nombre')
                ->get(['tipo_oficina_id', 'nombre']),
            'estados' => Estado::orderBy('nombre')->get(['estado_id', 'nombre']),
        ]);
    }
}
