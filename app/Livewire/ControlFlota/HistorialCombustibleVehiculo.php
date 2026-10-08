<?php

namespace App\Livewire\ControlFlota;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Vehiculo;
use App\Models\CargaCombustible;

#[Layout('layouts.app')]
class HistorialCombustibleVehiculo extends Component
{
    use WithPagination;

    public $vehiculoId;
    public $vehiculoNombre = '';
    public $vehiculoPlaca = '';
    public $fechaInicio = null;
    public $fechaFin = null;
    public $perPage = 10;
    public $page = 1;
    public $totalLitros = 0;
    public $totalCostos = 0;
    public $totalCargas = 0;

    public function mount($vehiculoId)
    {
        $vehiculo = Vehiculo::findOrFail($vehiculoId);
        $this->vehiculoId = $vehiculo->vehiculo_id;
        $this->vehiculoNombre = $vehiculo->marca . ' ' . $vehiculo->modelo;
        $this->vehiculoPlaca = $vehiculo->placa;
    }

    public function render()
    {
        $query = CargaCombustible::where('vehiculo_id', $this->vehiculoId);
        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }
        // Calcular totales sin paginar
        $allCargas = (clone $query)->get();
        $this->totalLitros = $allCargas->sum('litros');
        $this->totalCostos = $allCargas->sum('costo_total');
        $this->totalCargas = $allCargas->count();
        $historial = $query->orderByDesc('fecha')->paginate($this->perPage);
        return view('livewire.control-flota.historial-combustible-vehiculo', [
            'historial' => $historial,
            'totalLitros' => $this->totalLitros,
            'totalCostos' => $this->totalCostos,
            'totalCargas' => $this->totalCargas,
        ]);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function exportarHistorialPDF()
    {
        $vehiculo = Vehiculo::findOrFail($this->vehiculoId);
        $query = CargaCombustible::where('vehiculo_id', $vehiculo->vehiculo_id);
        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }
        $cargas = $query->orderByDesc('fecha')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.historial-combustible', [
            'vehiculo' => $vehiculo,
            'cargas' => $cargas,
        ])->setPaper('a4', 'portrait');
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'HistorialCombustible_' . $vehiculo->placa . '.pdf'
        );
    }

    public function exportarHistorialExcel()
    {
        $vehiculo = Vehiculo::findOrFail($this->vehiculoId);
        $query = CargaCombustible::where('vehiculo_id', $vehiculo->vehiculo_id);
        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }
        $cargas = $query->orderByDesc('fecha')->get();
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\HistorialCombustibleExport($vehiculo, $cargas),
            'HistorialCombustible_' . $vehiculo->placa . '.xlsx'
        );
    }
}
