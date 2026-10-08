<?php

namespace App\Livewire\Viajes;

use App\Models\Viaje;
use Livewire\Component;
use App\Models\Vehiculo;
use App\Models\UsuarioSeguimiento;
use App\Models\Proveedor;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\Viajes\EditForm;
use App\Livewire\Forms\Viajes\CreateForm;
use App\Models\RutasModelo;

#[Layout('layouts.app')] 
class MostrarViajes extends Component
{

    use WithPagination;

    public $search; // Campo de búsqueda
    public $filter = 'all'; // Filtro por defecto
    public $perPage = 5; // Registros por página
    public $sortDir = 'ASC';
    public $sortBy = 'ruta_id'; // Columna por la que se ordena
    public CreateForm $createForm;
    public EditForm $editForm;
    public $rutas;
    public $proveedores = [];
    public $vehiculos = [];
    public $trips = [];
    public $filteredProveedores = [];


    public function mount()
    {
        $this->proveedores = Proveedor::where('propio', false)->get();
        $this->rutas = RutasModelo::where('activo', true)->get();

        $this->trips[] = [
            'dia_semana' => '',
            'proveedor_id' => '',
            'vehiculo_id' => ''
        ];
    }
    
    public function resetFields()
    {
        $this->proveedores = [];
        $this->vehiculos = [];
        $this->trips = []; // Si quieres resetear también los viajes
    }
    public function addAnotherTrip()
    {
        $this->trips[] = [
            'dia_semana' => null,
            'proveedor_id' => null,
            'vehiculo_id' => null,
        ];
    }
    public function getVehiculosByProveedor($proveedorId)
    {
        return $proveedorId ? Vehiculo::where('proveedor_id', $proveedorId)->get() : collect();
    }

    public function updatedTrips($value, $name)
{
    // Detectamos el índice del viaje y el campo que se actualizó
    $matches = [];
    preg_match('/trips\.(\d+)\.proveedor_id/', $name, $matches);

    if (isset($matches[1])) {
        $index = $matches[1];

        // Obtener los vehículos asociados al proveedor seleccionado
        $proveedorId = $this->trips[$index]['proveedor_id'];
        $this->trips[$index]['vehiculos_disponibles'] = $proveedorId 
            ? Vehiculo::where('proveedor_id', $proveedorId)->get()
            : [];
    }
}

    public function removeTrip($index)
    {
        unset($this->trips[$index]);
        $this->trips = array_values($this->trips); // Re-indexa el arreglo para evitar problemas en el bucle
    }

    public function updatedCreateFormRuta($rutaId)
    {

        $this->resetFields();

           $this->proveedores = Proveedor::whereHas('rutas', function ($query) use ($rutaId) {
            $query->where('proveedor_ruta.ruta_id', $rutaId);
        })->get();
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->createForm['open'] = false; // Cierra el modal
    }

    public function updatedCreateFormProveedorId($proveedor_id)
    {
        $this->vehiculos = Vehiculo::where('proveedor_id', $proveedor_id)->get();
    }

    public function desactivar(Viaje $viaje)
    {
        $viaje->activo = false;
        $viaje->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el viaje ({$viaje->viaje_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Viaje Desactivado exitosamente!');
    }
    public function activar(Viaje $viaje)
    {
        $viaje->activo = true;
        $viaje->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó el viaje ({$viaje->viaje_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Viaje Activado exitosamente!');
    }

    public function edit(Viaje $viaje)
    {
        $this->resetValidation();
        $this->editForm->edit($viaje, );
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Viaje actualizado exitosamente!');
    }

    public function create()
    {
        $this->resetValidation();
        $this->trips = []; // Asegúrate de que esté limpio al empezar
        $this->createForm->create(); // Llama el método create() de CreateForm
    }
    
    public function store()
    {
        $this->validate();
        $this->createForm->trips = $this->trips;
        $this->createForm->store(); // Llama a la función 'store' para almacenar los datos
        $this->dispatch('alertSuccess', message: 'Viajes creados exitosamente!');
    }
    

    public function render()
    {
    
        $rutas = RutasModelo::where('activo', true) // Filtra solo las rutas activas
        ->when($this->search, function($query) {
            $query->where('ruta', 'LIKE', '%'. $this->search.'%');
        })
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);
        
        $viajes = Viaje::when($this->search, function($query) {
            $query->where('codigo', 'LIKE', '%' . $this->search . '%');
        })
        ->whereNotNull('proveedor_id') // Solo incluir viajes con proveedor_id disponible
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);
    
        return view('livewire.viajes.mostrar-viajes', [
            'viajes' => $viajes, 
            'rutas' => $rutas// Pasar los vehículos a la vista
        ]);
    }
}
