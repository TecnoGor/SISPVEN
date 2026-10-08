<?php

namespace App\Livewire\ControlFlota;

use Livewire\Component;
use App\Models\Vehiculo;
use App\Models\Mantenimiento;
use Livewire\Attributes\Layout;
use Livewire\WithPagination; // Importar el trait
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

#[Layout('layouts.app')]
class MantenimientosHoy extends Component
{
    use WithPagination; // Agregar el trait de paginación

    public $expandedMantenimientoId = null;
    public $vehiculosIds;
    public $vehiculos;
    public $search;
    public $filter = 'all'; 
    public $perPage = 5; 
    public $sortBy = 'vehiculo_id';
    public $sortDir = 'ASC';

    public function toggleDetails($mantenimientoId)
    {
        $this->expandedMantenimientoId = ($this->expandedMantenimientoId === $mantenimientoId) ? null : $mantenimientoId;
    }

    public function render()
    {
        $usuario = auth()->user();
$hoy = now()->toDateString();

if ($usuario->hasRole('SuperAdmin')) {
    $mantenimientos = Mantenimiento::with(['detalles', 'vehiculo.oficina']) // Incluye la oficina del vehículo
        ->whereDate('fecha', $hoy)
        ->paginate($this->perPage);
} elseif ($usuario->hasRole('Gerente de Estado')) {
    $estadoId = $usuario->usuarioEstado->id_estado;
    $mantenimientos = Mantenimiento::with(['detalles', 'vehiculo.oficina']) // Incluye la oficina del vehículo
        ->whereDate('fecha', $hoy)
        ->whereHas('vehiculo.oficina', function ($query) use ($estadoId) {
            $query->where('estado_id', $estadoId);
        })
        ->paginate($this->perPage);
} else {
    $oficinaId = $usuario->oficina_id;
    $mantenimientos = Mantenimiento::with(['detalles', 'vehiculo.oficina']) // Incluye la oficina del vehículo
        ->whereDate('fecha', $hoy)
        ->whereHas('vehiculo', function ($query) use ($oficinaId) {
            $query->where('oficina_id', $oficinaId);
        })
        ->paginate($this->perPage);
}

$this->vehiculosIds = $mantenimientos->pluck('vehiculo_id')->toArray();

$this->vehiculos = Vehiculo::whereIn('vehiculo_id', $this->vehiculosIds)
    ->when($this->search, function ($query) {
        $query->where('placa', 'LIKE', '%' . $this->search . '%');
    })
    ->get();

        return view('livewire.control-flota.mantenimientos-hoy', [
            'mantenimientos' => $mantenimientos,
            'vehiculos' => $this->vehiculos
        ]);
    }

    public function generarPDF()
    {
        $usuario = auth()->user();
        $hoy = now()->toDateString();
    
        if ($usuario->hasRole('SuperAdmin')) {
            $mantenimientos = Mantenimiento::with(['detalles', 'vehiculo.oficina'])
                ->whereDate('fecha', $hoy)
                ->get();
        } elseif ($usuario->hasRole('Gerente de Estado')) {
            $estadoId = $usuario->usuarioEstado->id_estado;
            $mantenimientos = Mantenimiento::with(['detalles', 'vehiculo.oficina'])
                ->whereDate('fecha', $hoy)
                ->whereHas('vehiculo.oficina', function ($query) use ($estadoId) {
                    $query->where('estado_id', $estadoId);
                })
                ->get();
        } else {
            $oficinaId = $usuario->oficina_id;
            $mantenimientos = Mantenimiento::with(['detalles', 'vehiculo.oficina'])
                ->whereDate('fecha', $hoy)
                ->whereHas('vehiculo', function ($query) use ($oficinaId) {
                    $query->where('oficina_id', $oficinaId);
                })
                ->get();
        }
    
        $vehiculosIds = $mantenimientos->pluck('vehiculo_id')->toArray();
        $vehiculos = Vehiculo::whereIn('vehiculo_id', $vehiculosIds)->get();
    
        $pdf = Pdf::loadView('pdf.mantenimientos-hoy', [
            'mantenimientos' => $mantenimientos,
            'vehiculos' => $vehiculos,
        ]);
    
        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'reporte_mantenimientos_hoy.pdf'
        );
    }
    


}


