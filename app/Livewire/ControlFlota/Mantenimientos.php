<?php

namespace App\Livewire\ControlFlota;

use Livewire\Component;
use App\Models\Vehiculo;
use Livewire\WithPagination;
use App\Models\Mantenimiento;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Mantenimientos extends Component
{
    use WithPagination;

    public $vehiculo_id;
    public $vehiculo;
    public $expandedMantenimientoId = null;
    public $perPage = 5;

    public function mount($vehiculo_id)
    {
        // Inicializar la propiedad $vehiculo con la información del vehículo
        $this->vehiculo = Vehiculo::where('vehiculo_id', $vehiculo_id)->first();
    }

    // Método para alternar los detalles de cada mantenimiento
    public function toggleDetails($mantenimientoId)
    {
        $this->expandedMantenimientoId = ($this->expandedMantenimientoId === $mantenimientoId) ? null : $mantenimientoId;
    }

    // Método renderizado para mostrar la vista con los mantenimientos
    public function render()
    {
        // Obtener los mantenimientos con la paginación
        $mantenimientos = Mantenimiento::with('detalles')
            ->where('vehiculo_id', $this->vehiculo_id)
            ->paginate($this->perPage);

        return view('livewire.control-flota.mantenimientos', [
            'vehiculo' => $this->vehiculo,
            'mantenimientos' => $mantenimientos,
        ]);
    }
}
