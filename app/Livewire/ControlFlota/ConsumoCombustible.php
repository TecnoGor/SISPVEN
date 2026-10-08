<?php

namespace App\Livewire\ControlFlota;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Vehiculo;
use App\Models\CargaCombustible;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

#[Layout('layouts.app')]
class ConsumoCombustible extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $historialModalOpen = false;
    public $historialVehiculoNombre = '';
    public $historialVehiculoPlaca = '';
    public $historialFechaInicio = null;
    public $historialFechaFin = null;
    public $historialPage = 1;
    public $historialPerPage = 10;
    public $historialVehiculoId = null;

    public function render()
    {
        $usuario = auth()->user();
        $oficinaIdUsuario = $usuario->oficina_id;

        $vehiculosQuery = Vehiculo::whereNotNull('oficina_id');
        
        // Aplicar filtros según el rol del usuario
        if ($usuario->id == 1) {
            // SuperAdmin (ID 1) ve todos los vehículos de todas las oficinas
            // No aplicar filtro adicional
        } else {
            // Otros usuarios ven solo vehículos de su oficina
            $oficinaId = $usuario->oficina_id;
            if ($oficinaId) {
                $vehiculosQuery->where('oficina_id', $oficinaId);
            }
        }
        
        $vehiculosQuery->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('placa', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('marca', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('modelo', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->orderBy('vehiculo_id', 'DESC');

        $vehiculos = $vehiculosQuery->paginate($this->perPage);

        // Para cada vehículo, calcular los consumos
        $vehiculosConsumo = $vehiculos->getCollection()->map(function($vehiculo) {
            $cargas = CargaCombustible::where('vehiculo_id', $vehiculo->vehiculo_id)->orderBy('fecha')->get();
            $litrosTotales = $cargas->sum('litros');
            $costoTotal = $cargas->sum('costo_total');
            $kilometrajeInicial = $vehiculo->kilometraje_actual;
            $kilometrajeFinal = $cargas->last()?->kilometraje ?? $kilometrajeInicial;
            $kmRecorridos = $kilometrajeFinal - $kilometrajeInicial;
            $consumoPor100km = $kmRecorridos > 0 ? ($litrosTotales / $kmRecorridos) * 100 : null;
            $kmPorLitro = $litrosTotales > 0 ? ($kmRecorridos / $litrosTotales) : null;
            return [
                'vehiculo' => $vehiculo,
                'litrosTotales' => $litrosTotales,
                'costoTotal' => $costoTotal,
                'kilometrajeInicial' => $kilometrajeInicial,
                'kilometrajeFinal' => $kilometrajeFinal,
                'kmRecorridos' => $kmRecorridos,
                'consumoPor100km' => $consumoPor100km,
                'kmPorLitro' => $kmPorLitro,
            ];
        });
        // Reemplazar la colección paginada por la extendida con datos de consumo
        $vehiculos->setCollection($vehiculosConsumo);

        // Calcular el historial como array simple filtrado por fechas
        $historialVehiculo = [];
        if ($this->historialModalOpen && $this->historialVehiculoId) {
            $query = \App\Models\CargaCombustible::where('vehiculo_id', $this->historialVehiculoId);
            if ($this->historialFechaInicio) {
                $query->whereDate('fecha', '>=', $this->historialFechaInicio);
            }
            if ($this->historialFechaFin) {
                $query->whereDate('fecha', '<=', $this->historialFechaFin);
            }
            $historialVehiculo = $query->orderByDesc('fecha')->get()->toArray();
        }

        // Obtener información de la oficina del usuario
        $infoOficina = $this->getInfoOficina($usuario);

        return view('livewire.control-flota.consumo-combustible', [
            'vehiculos' => $vehiculos,
            'historialVehiculo' => $historialVehiculo,
            'infoOficina' => $infoOficina,
        ]);
    }

    public function openHistorial($vehiculoId)
    {
        $this->reset([
            'historialVehiculoId', 'historialVehiculoNombre', 'historialVehiculoPlaca',
            'historialModalOpen', 'historialFechaInicio', 'historialFechaFin', 'historialPage', 'historialPerPage'
        ]);
        $vehiculo = \App\Models\Vehiculo::findOrFail($vehiculoId);
        $this->historialVehiculoId = $vehiculo->vehiculo_id;
        $this->historialVehiculoNombre = $vehiculo->marca . ' ' . $vehiculo->modelo;
        $this->historialVehiculoPlaca = $vehiculo->placa;
        $this->historialModalOpen = true;
    }

    public function updatedHistorialFechaInicio()
    {
        $this->historialPage = 1;
    }
    public function updatedHistorialFechaFin()
    {
        $this->historialPage = 1;
    }
    public function updatedHistorialPerPage()
    {
        $this->historialPage = 1;
    }
    public function updatingHistorialPage()
    {
    }

    public function closeModal()
    {
        $this->historialModalOpen = false;
    }

    public function exportarHistorialPDF($vehiculoPlaca)
    {
        $vehiculo = \App\Models\Vehiculo::where('placa', $vehiculoPlaca)->firstOrFail();
        $query = \App\Models\CargaCombustible::where('vehiculo_id', $vehiculo->vehiculo_id);
        if ($this->historialFechaInicio) {
            $query->whereDate('fecha', '>=', $this->historialFechaInicio);
        }
        if ($this->historialFechaFin) {
            $query->whereDate('fecha', '<=', $this->historialFechaFin);
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

    public function exportarHistorialExcel($vehiculoPlaca)
    {
        $vehiculo = \App\Models\Vehiculo::where('placa', $vehiculoPlaca)->firstOrFail();
        $query = \App\Models\CargaCombustible::where('vehiculo_id', $vehiculo->vehiculo_id);
        if ($this->historialFechaInicio) {
            $query->whereDate('fecha', '>=', $this->historialFechaInicio);
        }
        if ($this->historialFechaFin) {
            $query->whereDate('fecha', '<=', $this->historialFechaFin);
        }
        $cargas = $query->orderByDesc('fecha')->get();
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\HistorialCombustibleExport($vehiculo, $cargas),
            'HistorialCombustible_' . $vehiculo->placa . '.xlsx'
        );
    }

    /**
     * Devuelve un paginador válido para el historial del modal, aunque esté vacío.
     */
    private function getHistorialVehiculoPaginator()
    {
        if ($this->historialVehiculo instanceof \Illuminate\Pagination\AbstractPaginator) {
            return $this->historialVehiculo;
        }
        // Si es array o null, devolver paginador vacío
        $perPage = $this->historialPerPage ?? 10;
        return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
    }

    /**
     * Obtiene información de la oficina del usuario para mostrar en las gráficas
     */
    private function getInfoOficina($usuario)
    {
        $info = [
            'nombre' => 'Todas las oficinas',
            'tipo' => 'SuperAdmin',
            'totalVehiculos' => 0
        ];

        if ($usuario->id == 1) {
            // SuperAdmin ve todas las oficinas
            $info['tipo'] = 'SuperAdmin';
            $info['totalVehiculos'] = Vehiculo::whereNotNull('oficina_id')->count();
        } else {
            // Otros usuarios ven solo su oficina
            $oficinaId = $usuario->oficina_id;
            if ($oficinaId) {
                $oficina = \App\Models\Oficina::find($oficinaId);
                $info['nombre'] = $oficina ? $oficina->nombre : 'Oficina';
                $info['tipo'] = 'Oficina específica';
                $info['totalVehiculos'] = Vehiculo::where('oficina_id', $oficinaId)->count();
            }
        }

        return $info;
    }
} 