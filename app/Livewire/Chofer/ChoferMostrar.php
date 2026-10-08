<?php

namespace App\Livewire\Chofer;

use App\Models\Chofer;
use Livewire\Component;
use App\Models\Vehiculo;
use App\Models\UsuarioSeguimiento;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\Choferes\EditForm;
use App\Livewire\Forms\Choferes\EditForm2;
use App\Livewire\Forms\Choferes\CreateForm;

#[Layout('layouts.app')] 
class ChoferMostrar extends Component
{
    public $proveedor_id;
    public $search;
    public $filter = 'all';
    public $perPage = 5;
    public $sortBy = 'chofer_id';
    public $sortDir = 'ASC';
    public CreateForm $createForm;
    public EditForm $editForm;

    public function create()
    {
        $this->resetValidation();
        $this->createForm->create();
    }

    public function store()
    {
        $this->createForm->store($this->proveedor_id);
        $this->dispatch('alertSuccess', message: 'Chofer creado exitosamente!');
    }
    
    public function desactivar(Chofer $chofer)
    {
        $chofer->activo = false;
        $chofer->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el chofer ({$chofer->chofer_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Chofer Desactivado exitosamente!');
    }
    public function activar(Chofer $chofer)
    {
        $chofer->activo = true;
        $chofer->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó el chofer ({$chofer->chofer_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Chofer Activado exitosamente!');
    }
    public function edit(Chofer $chofer)
    {
        $this->resetValidation();
        $this->editForm->edit($chofer);
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Chofer editado exitosamente!');
    }
    public function render()
    {

        $vehiculos = Vehiculo::where('proveedor_id', $this->proveedor_id)->get();


        $choferes = Chofer::when($this->proveedor_id, function($query) {
            $query->where('proveedor_id', $this->proveedor_id);
        })
        ->when($this->search, function($query) {
            $query->where('nombre', 'LIKE', '%' . $this->search . '%');
        })
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);
        return view('livewire.chofer.chofer-mostrar', [
            'choferes' => $choferes,
            'vehiculos' => $vehiculos,
        ]);
    }
}
