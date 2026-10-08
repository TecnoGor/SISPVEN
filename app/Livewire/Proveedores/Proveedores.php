<?php

namespace App\Livewire\Proveedores;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\Proveedores\CreateForm;
use App\Models\Proveedor;
use App\Models\UsuarioSeguimiento;
use Livewire\WithPagination;
use App\Livewire\Forms\Proveedores\EditForm;
#[Layout('layouts.app')] 
class Proveedores extends Component
{
    public CreateForm $createForm;
    use WithPagination;
    public $search;
    public $filter = 'all';
    public $perPage = 10;
    public $sortBy = 'proveedor_id';
    public $sortDir = 'ASC';
    public EditForm $EditForm;

    
    public function create()
    {
        $this->resetValidation();
        $this->createForm->create();
    }

    public function store()
    {
        $this->createForm->store();
        $this->dispatch('alertSuccess', message: 'Rol creado exitosamente!');
    }
    public function edit(Proveedor $proveedor)
    {
        $this->resetValidation();
        $this->EditForm->edit($proveedor);
    }
    public function update()
    {
        $this->EditForm->update();
        $this->dispatch('alertSuccess', message: 'Rol editado exitosamente!');
    }

    public function desactivar(Proveedor $proveedor)
    {
        $proveedor->activo = false;
        $proveedor->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el proveedor ({$proveedor->proveedor_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Proveedor Desactivada exitosamente!');
        $this->dispatch('tarifaUpdated');
    }
    public function activar(Proveedor $proveedor)
    {
        $proveedor->activo = true;
        $proveedor->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó el proveedor ({$proveedor->proveedor_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Proveedor Activada exitosamente!');
        $this->dispatch('tarifaUpdated');
    }

    public function render()
    {
        $proveedores = Proveedor::when($this->search, function($query) {
            $query->where('representante_legal', 'LIKE', '%' . $this->search . '%');
        })
        ->where('propio', false) // Filtra solo los proveedores con "propio" en false
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);
        return view('livewire.proveedores.proveedores', compact('proveedores'));
    }
}
