<?php

namespace App\Livewire\ServiciosFlota;

use Livewire\Component;
use App\Models\ServicioFlota;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\ServiciosFlota\EditForm;
use App\Livewire\Forms\ServiciosFlota\CreateForm;

#[Layout('layouts.app')] 
class ServiciosFlota extends Component
{
    public $search;
    public $filter = 'all'; 
    public $perPage = 5; 
    public $sortBy = 'servicios_flota_id';
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
        $this->createForm->store();
        $this->dispatch('alertSuccess', message: 'Servicio creado exitosamente!');
    }
    
    public function desactivar(ServicioFlota $servicio)
    {
        $servicio->activo = false;
        $servicio->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el servicio de flota ({$servicio->servicios_flota_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Servicio Desactivado exitosamente!');
    }
    public function activar(ServicioFlota $servicio)
    {
        $servicio->activo = true;
        $servicio->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó el servicio de flota ({$servicio->servicios_flota_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Servicio Activado exitosamente!');
    }

    public function edit(ServicioFlota $servicio)
    {
        $this->resetValidation();
        $this->editForm->edit($servicio);
    }
    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Servicio editado exitosamente!');
    }

    public function render()
    {
        $servicios = ServicioFlota::when($this->filter !== 'all', function ($query) {
            // Agrega aquí más condiciones según los posibles valores del filtro
            // Ejemplo: Filtrar solo servicios activos/inactivos
            if ($this->filter === 'activos') {
                $query->where('activo', true);
            } elseif ($this->filter === 'inactivos') {
                $query->where('activo', false);
            }
        })
        ->orderBy($this->sortBy, $this->sortDir) // Usa las variables de ordenación
        ->paginate($this->perPage); // Aplica la paginación dinámica

        return view('livewire.servicio-flota.servicios-flota', compact('servicios'));
    }
}
