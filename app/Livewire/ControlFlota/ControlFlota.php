<?php

namespace App\Livewire\ControlFlota;

use Livewire\Component;
use App\Models\Vehiculo;
use Livewire\WithPagination;
use App\Models\Mantenimiento;
use App\Models\VehiculoPropio;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\ControlFlota\editForm;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\VehiculosExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

#[Layout('layouts.app')]
class ControlFlota extends Component
{
    use WithPagination;

    public $search;
    public $filter = 'all'; 
    public $perPage = 5; 
    public $sortBy = 'vehiculo_id';
    public $sortDir = 'ASC';
    public editForm $editForm;
    public $mantenimientos = [];
    public $expandedMantenimientoId = null;
    public $searchMantenimiento = '';
    public $startDate = null;
    public $endDate = null;
    public $mantenimiento_page = 1;
    // Eliminado: public $mantenimientosPaginados;
    public $vehiculoActual = null;

    public function edit(Vehiculo $vehiculo)
    {
        $this->vehiculoActual = $vehiculo;
        $this->actualizarMantenimientos();
        $this->editForm->edit($vehiculo);
    }

    public function toggleDetails($mantenimientoId)
    {
        $this->expandedMantenimientoId = ($this->expandedMantenimientoId === $mantenimientoId) ? null : $mantenimientoId;
    }

    public function update()
    {
        //
    }

    private function getVehiculosQuery()
    {
        $usuario = auth()->user();
        $oficinaIdUsuario = $usuario->oficina_id;
        $query = Vehiculo::with('oficina')->whereNotNull('oficina_id');

        if ($usuario->hasRole('SuperAdmin') || $usuario->can('Vehiculos Totales Control Flota')) {
            // No filtro extra — ve todos los vehículos
        } else {
            $query
                // Gerente de Estado: vehículos de todas las oficinas de su estado
                ->when($usuario->hasRole('Gerente de Estado'), function ($query) use ($usuario) {
                    $estadoId = optional($usuario->usuarioEstado)->id_estado;
                    if ($estadoId) {
                        $oficinasIds = \App\Models\Oficina::where('estado_id', $estadoId)->pluck('oficina_id');
                        $query->whereIn('oficina_id', $oficinasIds);
                    }
                })
                // Permiso Control flota: solo vehículos de su oficina
                ->when($usuario->can('Control flota'), function ($query) use ($usuario) {
                    $oficinaId = $usuario->oficina_id;
                    if ($oficinaId) {
                        $query->where('oficina_id', $oficinaId);
                    }
                })
                // Filtros originales
                ->when($usuario->can('Vehiculos Propios'), function ($query) use ($usuario) {
                    $estadoId = DB::table('usuario_estados')
                        ->where('id_user', $usuario->id)
                        ->value('estado_id');
                    if ($estadoId) {
                        $query->whereHas('oficina', function ($q) use ($estadoId) {
                            $q->where('estado_id', $estadoId);
                        });
                    }
                })
                ->when($usuario->hasRole('Jefe de OPT') && $oficinaIdUsuario, function ($query) use ($oficinaIdUsuario) {
                    $query->where('oficina_id', $oficinaIdUsuario);
                });
        }

        // Filtro de búsqueda
        if ($this->search) {
            $query->where('placa', 'LIKE', '%' . $this->search . '%');
        }
        $query->orderBy($this->sortBy, $this->sortDir);
        return $query;
    }

    public function render()
    {
        $vehiculos = $this->getVehiculosQuery()->paginate($this->perPage);
        $mantenimientosPaginados = null;
        if ($this->vehiculoActual) {
            $query = Mantenimiento::with('detalles.servicioFlota')
                ->where('vehiculo_id', $this->vehiculoActual->vehiculo_id);
            if ($this->searchMantenimiento) {
                $query->where('descripcion', 'like', '%' . $this->searchMantenimiento . '%');
            }
            if ($this->startDate) {
                $query->whereDate('created_at', '>=', $this->startDate);
            }
            if ($this->endDate) {
                $query->whereDate('created_at', '<=', $this->endDate);
            }
            $query->orderByDesc('created_at');
            $mantenimientosPaginados = $query->paginate(10, ['*'], 'mantenimiento_page', $this->mantenimiento_page);
        }
        return view('livewire.control-flota.control-flota', [
            'vehiculos' => $vehiculos,
            'mantenimientosPaginados' => $mantenimientosPaginados,
            'vehiculoActual' => $this->vehiculoActual,
        ]);
    }

    public function exportarPDF()
    {
        $vehiculos = $this->getVehiculosQuery()->get();
        $usuario = auth()->user();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.flota-reporte', [
            'vehiculos' => $vehiculos,
            'usuario' => $usuario
        ])->setPaper('a4', 'portrait');
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'control_flota.pdf');
    }

    public function exportarExcel()
    {
        $vehiculos = $this->getVehiculosQuery()->get();
        $usuario = auth()->user();
        $oficinaNombre = $usuario->oficina->nombre ?? 'Sin Oficina';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\VehiculosExport($vehiculos, $oficinaNombre),
            'control_flota.xlsx'
        );
    }
    
    public function generarReportePDF($vehiculoId, $searchMantenimiento = '', $startDate = null, $endDate = null, $mantenimiento_page = 1)
    {
        $vehiculo = Vehiculo::with(['mantenimientos.detalles.servicioFlota'])->findOrFail($vehiculoId);
        $query = Mantenimiento::with('detalles.servicioFlota')
            ->where('vehiculo_id', $vehiculo->vehiculo_id);
        if ($searchMantenimiento) {
            $query->where('descripcion', 'like', '%' . $searchMantenimiento . '%');
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        $query->orderByDesc('created_at');
        $mantenimientos = $query->paginate(10, ['*'], 'mantenimiento_page', $mantenimiento_page);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.mantenimientos', [
            'vehiculo' => $vehiculo,
            'mantenimientos' => $mantenimientos,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'Mantenimientos_' . $vehiculo->placa . '.pdf'
        );
    }

    public function generarReporteExcel($vehiculoId, $searchMantenimiento = '', $startDate = null, $endDate = null, $mantenimiento_page = 1)
    {
        $vehiculo = Vehiculo::with(['mantenimientos.detalles.servicioFlota'])->findOrFail($vehiculoId);
        $query = Mantenimiento::with('detalles.servicioFlota')
            ->where('vehiculo_id', $vehiculo->vehiculo_id);
        if ($searchMantenimiento) {
            $query->where('descripcion', 'like', '%' . $searchMantenimiento . '%');
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        $query->orderByDesc('created_at');
        $mantenimientos = $query->paginate(10, ['*'], 'mantenimiento_page', $mantenimiento_page);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MantenimientosVehiculoExport($vehiculo, $mantenimientos),
            'Mantenimientos_' . $vehiculo->placa . '.xlsx'
        );
    }
    
    public function updatedSearchMantenimiento()
    {
        $this->mantenimiento_page = 1;
        $this->actualizarMantenimientos();
    }
    public function updatedStartDate()
    {
        $this->mantenimiento_page = 1;
        $this->actualizarMantenimientos();
    }
    public function updatedEndDate()
    {
        $this->mantenimiento_page = 1;
        $this->actualizarMantenimientos();
    }
    public function updatingMantenimientoPage()
    {
        $this->actualizarMantenimientos();
    }

    public function actualizarMantenimientos()
    {
        if (!$this->vehiculoActual) return;
        $query = Mantenimiento::with('detalles.servicioFlota')
            ->where('vehiculo_id', $this->vehiculoActual->vehiculo_id);
        if ($this->searchMantenimiento) {
            $query->where('descripcion', 'like', '%' . $this->searchMantenimiento . '%');
        }
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }
        $query->orderByDesc('created_at');
        $this->mantenimientosPaginados = $query->paginate(10, ['*'], 'mantenimiento_page', $this->mantenimiento_page);
    }

    public function generarReportePDFDirect($vehiculo, $mantenimientos)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.mantenimientos', [
            'vehiculo' => (object)$vehiculo,
            'mantenimientos' => collect($mantenimientos),
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'Mantenimientos_' . $vehiculo['placa'] . '.pdf'
        );
    }

    public function generarReporteExcelDirect($vehiculo, $mantenimientos)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MantenimientosVehiculoExport((object)$vehiculo, collect($mantenimientos)),
            'Mantenimientos_' . $vehiculo['placa'] . '.xlsx'
        );
    }
}
