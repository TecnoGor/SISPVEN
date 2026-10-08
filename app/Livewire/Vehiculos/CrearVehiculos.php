<?php

namespace App\Livewire\Vehiculos;

use App\Models\Chofer;
use Livewire\Component;
use App\Models\Vehiculo;
use App\Models\UsuarioSeguimiento;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\Vehiculos\EditForm;
use App\Livewire\Forms\Vehiculos\EditForm2;
use App\Livewire\Forms\Vehiculos\CreateForm;

#[Layout('layouts.app')] 
class CrearVehiculos extends Component
{
    use WithPagination;

    public $proveedor_id; // Proveedor ID recibido
    public $search; // Campo de búsqueda
    public $filter = 'all'; // Filtro por defecto
    public $perPage = 5; // Registros por página
    public $sortBy = 'chofer_id'; // Columna por la que se ordena
    public $sortDir = 'ASC'; // Dirección de ordenamiento
    public CreateForm $createForm;
    public EditForm2 $editForm2;
    public EditForm $editForm;

    public function mount($proveedor_id)
    {
        $this->proveedor_id = $proveedor_id; // Asigna el proveedor ID
    }
    public function create()
    {
        $this->resetValidation();
        $this->createForm->create();
    }

    public function store()
    {
        $this->createForm->store($this->proveedor_id);
        $this->dispatch('alertSuccess', message: 'Vehiculo creado exitosamente!');
    }


    public function desactivar(Vehiculo $vehiculo)
    {
        $vehiculo->Activo = false;
        $vehiculo->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el vehículo ({$vehiculo->vehiculo_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Vehiculo Desactivado exitosamente!');
    }
    public function activar(Vehiculo $vehiculo)
    {
        $vehiculo->Activo = true;
        $vehiculo->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó el vehículo ({$vehiculo->vehiculo_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Vehiculo Activado exitosamente!');
    }

    public function edit(Vehiculo $vehiculo)
    {
        $this->resetValidation();
        $this->editForm->edit($vehiculo);
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Vehiculo editado exitosamente!');
    }

    public function edit2(Vehiculo $vehiculo)
    {
        $this->resetValidation();
        $this->editForm2->edit($vehiculo);
    }

    public function update2()
    {
        $this->editForm2->update();
        $this->dispatch('alertSuccess', message: 'Chofer Asignado exitosamente!');
    }

    public function render()
    {
        
        $choferes = Chofer::where('proveedor_id', $this->proveedor_id)
        ->where('activo', true)
        ->get();

        $vehiculos = Vehiculo::when($this->proveedor_id, function($query) {
                // Filtrar por proveedor_id
                $query->where('proveedor_id', $this->proveedor_id);
            })
            ->when($this->search, function($query) {
                // Filtrar por placa, como antes
                $query->where('placa', 'LIKE', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    
        return view('livewire.vehiculos.crear-vehiculos', [
            'vehiculos' => $vehiculos,
            'proveedor_id' => $this->proveedor_id,
            'choferes' => $choferes,
        ]);
    }
    
}
