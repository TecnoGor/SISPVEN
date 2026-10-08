<?php

namespace App\Livewire\Rutas;

use App\Models\Estado;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\Rutas\EditForm;
use App\Livewire\Forms\Rutas\CreateForm;
use App\Models\Oficina;
use App\Models\RutasModelo;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')] 
class Rutas extends Component
{

    use WithPagination;

    public $search; // Campo de búsqueda
    public $filter = 'all'; // Filtro por defecto
    public $perPage = 5; // Registros por página
    public $sortBy = 'ruta_id'; // Columna por la que se ordena
    public $sortDir = 'ASC'; // Dirección de ordenamiento
    public CreateForm $createForm;
    public EditForm $editForm;
    public $puntosEntrega = [];


    public $oficinas;

    public function mount()
    {
        $this->oficinas = Oficina::where('tipo_oficina_id', 4)->get();
    }

    public function create()
    {
        $this->resetValidation();
        $this->createForm->create();
    }

    public function addPuntoEntrega()
    {
        // Añade un nuevo punto de entrega al array
        $this->puntosEntrega[] = null;
    }
    
    public function removePuntoEntrega($index)
    {
        unset($this->puntosEntrega[$index]);
        $this->puntosEntrega = array_values($this->puntosEntrega);
    }

    public function store()
    {
        $this->createForm->store();
        $this->dispatch('alertSuccess', message: 'Ruta creada exitosamente!');
    }
    public function edit(RutasModelo $ruta)
    {
        $this->resetValidation();
        $this->editForm->edit($ruta);
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Ruta editada exitosamente!');
    }


    public function desactivar(RutasModelo $ruta)
    {
        $ruta->activo = false;
        $ruta->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó la ruta ({$ruta->ruta_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Ruta Desactivada exitosamente!');
    }
    public function activar(RutasModelo $ruta)
    {
        $ruta->activo = true;
        $ruta->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó la ruta ({$ruta->ruta_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Ruta Activada exitosamente!');
    }

    public function render()
    {
        $rutas = RutasModelo::when($this->search, function($query) {
            $query->where('ruta', 'LIKE', '%' . $this->search . '%');
        })
        ->whereHas('oficinaOrigen', function($query) {
            $query->where('tipo_oficina_id', 4);
        })
        ->whereHas('oficinaDestino', function($query) {
            $query->where('tipo_oficina_id', 4);
        })
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);

        return view('livewire.rutas.rutas', [
            'rutas' => $rutas, // Pasar los vehículos a la vista
        ]);
    }
}
