<?php

namespace App\Livewire\AlmacenOficinas;

use App\Models\Envio;
use App\Models\RegistroEntrega;
use App\Models\Oficina;
use App\Models\Estado;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AlmacenOficinas extends Component
{
    use WithPagination;

    public $oficina_id = '';
    public $estado_id  = '';
    public $desde = '';
    public $hasta = '';
    public $filterStatus = true; // true = Creados, false = Entregados
    public $search   = '';
    public $perPage  = 15;
    public $oficinas = [];
    public $estados  = [];

    public function mount()
    {
        $this->authorize('Ver Informacion de Envíos');
        $this->estados  = Estado::orderBy('nombre')->get();
        $this->oficinas = Oficina::orderBy('nombre')->get(['oficina_id', 'nombre']);
    }

    public function updatedEstadoId($value)
    {
        $this->resetPage();
        $this->oficina_id = '';
        $this->oficinas = $value
            ? Oficina::where('estado_id', $value)->orderBy('nombre')->get(['oficina_id', 'nombre'])
            : Oficina::orderBy('nombre')->get(['oficina_id', 'nombre']);
    }

    public function updatingOficinaId()
    {
        $this->resetPage();
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingDesde()
    {
        $this->resetPage();
    }
    public function updatingHasta()
    {
        $this->resetPage();
    }

    public function setStatus(bool $status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function render()
    {
        if ($this->filterStatus) {
            // ── PENDIENTES ── envíos creados que aún no tienen RegistroEntrega
            $envios = Envio::with(['oficinas', 'envio_encaminamientos.envio_estatus'])
                ->whereDoesntHave('registros_entregas')
                ->when($this->oficina_id, fn($q) => $q->where('oficina_id', $this->oficina_id))
                ->when($this->estado_id && !$this->oficina_id, function ($q) {
                    $oficinas = Oficina::where('estado_id', $this->estado_id)->pluck('oficina_id');
                    $q->whereIn('oficina_id', $oficinas);
                })
                ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
                ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
                ->when($this->search, function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('codigo_envio', 'like', '%' . $this->search . '%')
                            ->orWhere('nombre_rem',  'like', '%' . $this->search . '%')
                            ->orWhere('nombre_dest', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($this->perPage);
        } else {
            // ── ENTREGADOS ── usa registros_entregas (1 registro = 1 entrega)
            $envios = RegistroEntrega::with(['envio.envio_encaminamientos.envio_estatus', 'envio.oficinas'])
                ->when($this->oficina_id, fn($q) => $q->where('oficina_id', $this->oficina_id))
                ->when($this->estado_id && !$this->oficina_id, function ($q) {
                    $oficinas = Oficina::where('estado_id', $this->estado_id)->pluck('oficina_id');
                    $q->whereIn('oficina_id', $oficinas);
                })
                ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
                ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
                ->when($this->search, function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('codigo_envio', 'like', '%' . $this->search . '%')
                           ->orWhereHas('envio', function ($q3) {
                               $q3->where('nombre_rem',  'like', '%' . $this->search . '%')
                                  ->orWhere('nombre_dest', 'like', '%' . $this->search . '%');
                           });
                    });
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($this->perPage);
        }

        return view('livewire.almacen-oficinas.almacen-oficinas', compact('envios'));
    }
}
