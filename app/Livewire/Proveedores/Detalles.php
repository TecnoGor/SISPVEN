<?php

namespace App\Livewire\Proveedores;

use App\Models\Chofer;
use Livewire\Component;
use App\Models\Proveedor;
use App\Models\Vehiculo;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')] 
class Detalles extends Component
{
    use WithPagination;
    public $proveedor_id;
    public $search;
    public $filter = 'all';
    public $perPage = 5;
    public $sortBy = 'proveedor_id';
    public $sortDir = 'ASC';

    public function render()
    
    {
        $vehiculos = Vehiculo::when($this->search, function($query) {
            $query->where('proveedor_id', 'LIKE', '%'. $this->search.'%');
        })
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);
        $proveedor = Proveedor::find($this->proveedor_id);
        return view('livewire.proveedores.detalles', [
            'proveedor' => $proveedor,
        ]);
    }
}
