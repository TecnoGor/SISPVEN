<?php

namespace App\Livewire;

use App\Models\Auditoria;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AuditoriaIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;

    public function render()
    {
        $logs = Auditoria::with('user')
            ->when($this->search, function ($query) {
                $query->where('descripcion', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('accion', 'LIKE', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'LIKE', '%' . $this->search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.auditoria-index', compact('logs'));
    }
}
