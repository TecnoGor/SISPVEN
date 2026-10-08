<?php

namespace App\Livewire\ControlFlota;

use Livewire\Component;
use App\Models\Mantenimiento;
use App\Models\ServicioFlota;
use App\Models\Vehiculo;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MantenimientosRangoExport;

#[Layout('layouts.app')]
class MantenimientosPorServicios extends Component
{
    public $expandedMantenimientoId = null;
    public $fechaDesde;
    public $fechaHasta;
    public $servicios;
    public $selectedServicio;
    public $mantenimientos;

    public function toggleDetails($mantenimientoId)
    {
        $this->expandedMantenimientoId = ($this->expandedMantenimientoId === $mantenimientoId) ? null : $mantenimientoId;
    }

    public function mount()
    {
        $this->servicios = ServicioFlota::all();
        $this->fechaDesde = now()->startOfMonth()->toDateString();
        $this->fechaHasta = now()->toDateString();
        $this->updateMantenimientos();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['fechaDesde', 'fechaHasta'])) {
            $this->updateMantenimientos();
        }
    }

    public function updateMantenimientos()
    {
        $usuario = auth()->user();

        $query = Mantenimiento::with('vehiculo.oficina')
            ->whereBetween('fecha', [$this->fechaDesde, $this->fechaHasta]);

        if ($usuario->hasRole('SuperAdmin') || !$usuario->roles->count()) {
            // No se aplica filtro extra
        } elseif ($usuario->hasRole('Gerente de Estado')) {
            $estadoId = $usuario->usuarioEstado->id_estado ?? null;

            $query->whereHas('vehiculo.oficina', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        } elseif ($usuario->hasRole('Jefe de OPT')) {
            $oficinaId = $usuario->oficina_id ?? null;

            if ($oficinaId) {
                $query->whereHas('vehiculo', function ($q) use ($oficinaId) {
                    $q->where('oficina_id', $oficinaId);
                });
            } else {
                $this->mantenimientos = [];
                return;
            }
        } else {
            $this->mantenimientos = [];
            return;
        }

        $this->mantenimientos = $query->get();
    }

    public function render()
    {
        return view('livewire.control-flota.mantenimientos-por-servicios', [
            'servicios' => $this->servicios,
            'mantenimientos' => $this->mantenimientos,
        ]);
    }
    public function generarPDFPorRango()
    {
        $usuario = auth()->user();
    
        $query = Mantenimiento::with(['vehiculo.oficina', 'detalles.servicioFlota'])
            ->whereBetween('fecha', [$this->fechaDesde, $this->fechaHasta]);
    
        if ($usuario->hasRole('SuperAdmin') || !$usuario->roles->count()) {
            // No se aplica filtro extra
        } elseif ($usuario->hasRole('Gerente de Estado')) {
            $estadoId = $usuario->usuarioEstado->id_estado ?? null;
    
            $query->whereHas('vehiculo.oficina', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        } elseif ($usuario->hasRole('Jefe de OPT')) {
            $oficinaId = $usuario->oficina_id ?? null;
    
            if ($oficinaId) {
                $query->whereHas('vehiculo', function ($q) use ($oficinaId) {
                    $q->where('oficina_id', $oficinaId);
                });
            } else {
                return response()->noContent(); // No se genera PDF si no tiene oficina
            }
        } else {
            return response()->noContent(); // No se genera PDF si el rol no aplica
        }
    
        $mantenimientos = $query->get();
    
        $vehiculos = $mantenimientos->pluck('vehiculo')->unique('vehiculo_id');
    
        $pdf = Pdf::loadView('pdf.mantenimientos-fecha', [
            'mantenimientos' => $mantenimientos,
            'vehiculos' => $vehiculos,
            'fechaDesde' => $this->fechaDesde,
            'fechaHasta' => $this->fechaHasta,
        ])->setPaper('A4', 'portrait');
    
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_mantenimientos_' . now()->format('Ymd_His') . '.pdf');
    }
    
    public function exportarExcelPorRango()
    {
        $usuario = auth()->user();
    
        $query = Mantenimiento::with(['vehiculo.oficina', 'detalles.servicioFlota'])
            ->whereBetween('fecha', [$this->fechaDesde, $this->fechaHasta]);
    
        if ($usuario->hasRole('SuperAdmin') || !$usuario->roles->count()) {
            // Sin filtro adicional
        } elseif ($usuario->hasRole('Gerente de Estado')) {
            $estadoId = $usuario->usuarioEstado->id_estado ?? null;
    
            $query->whereHas('vehiculo.oficina', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        } elseif ($usuario->hasRole('Jefe de OPT')) {
            $oficinaId = $usuario->oficina_id ?? null;
    
            if ($oficinaId) {
                $query->whereHas('vehiculo', function ($q) use ($oficinaId) {
                    $q->where('oficina_id', $oficinaId);
                });
            } else {
                return response()->noContent(); // No exporta si no tiene oficina
            }
        } else {
            return response()->noContent(); // No exporta si no aplica
        }
    
        $mantenimientos = $query->get();
    
        // Definir nombre de oficina
        $oficinaNombre = $usuario->oficina->nombre ?? 'Todas las oficinas';
    
        // Descargar el Excel pasando los 4 parámetros
        return Excel::download(
            new MantenimientosRangoExport(
                $mantenimientos,
                $this->fechaDesde,
                $this->fechaHasta,
                $oficinaNombre
            ),
            'reporte_mantenimientos_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

}
