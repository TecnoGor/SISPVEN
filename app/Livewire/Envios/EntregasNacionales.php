<?php

namespace App\Livewire\Envios;

use Carbon\Carbon;
use App\Models\RegistroEntrega;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EntregasExport;
use Dompdf\Dompdf;
use Dompdf\Options;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class EntregasNacionales extends Component
{
    public $desde, $hasta, $entregas, $totalEnvios, $totalMontos, $nombre; 

    public function mount()
    {
        $usuario = auth()->user();
        $this->nombre = $usuario->oficina->nombre;  // Obtener el nombre de la oficina una vez
    }

    public function render()
    {
        $usuario = auth()->user();

        $todos_servicios = Servicio::all();
        $usuario = auth()->user();
        $fechaInicio = $this->desde;
        $fechaFin = $this->hasta;

        if ((!$fechaInicio) || (!$fechaFin)) {
            $fechaInicio = now()->startOfDay();
            $fechaFin = now()->endOfDay();
        
            $this->desde = now()->format('Y-m-d');
            $this->hasta = now()->format('Y-m-d');
        } else {
            $fechaInicio = $fechaInicio . " 00:00:00";
            $fechaFin = $fechaFin . " 23:59:59";
        }
        
        // Determinar el rango de fechas según la selección del usuario
        $fechaInicio = $this->desde ? Carbon::parse($this->desde)->startOfDay() : Carbon::today()->startOfDay();
        $fechaFin = $this->hasta ? Carbon::parse($this->hasta)->endOfDay() : Carbon::today()->endOfDay();

        // Asegúrate de que las fechas estén formateadas correctamente
        $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($fechaFin)->endOfDay();

        // Consulta para obtener las entregas
        if ($usuario->hasRole('Jefe de OPT')) {
            $this->entregas = RegistroEntrega::where('oficina_id', $usuario->oficina_id)
                ->where('nacional?', true)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->select(
                    'servicio_id',
                    DB::raw('COUNT(*) as total_envios'),
                    DB::raw('SUM(costo_total) as costo_total')
                )
                ->groupBy('servicio_id')
                ->get();
        } else {
            $this->entregas = RegistroEntrega::where('usuario_id', $usuario->id)
                ->where('nacional?', true)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->select(
                    'servicio_id',
                    DB::raw('COUNT(*) as total_envios'),
                    DB::raw('SUM(costo_total) as costo_total')
                )
                ->groupBy('servicio_id')
                ->get();
        }

        $this->totalEnvios = $this->entregas->sum('total_envios');
        $this->totalMontos = $this->entregas->sum('costo_total');

        return view('livewire.envios.entregas-nacionales', [
            'entregas' => $this->entregas,
            'totalEnvios' => $this->totalEnvios,
            'totalMontos' => $this->totalMontos,
        ]);
    }
    public function exportarPDF()
    {
        $usuario = auth()->user();
    
        // Asegurar que las fechas estén correctamente formateadas
        $fechaInicio = Carbon::parse($this->desde)->startOfDay();
        $fechaFin = Carbon::parse($this->hasta)->endOfDay();
    
        // Obtener los datos de entregas nacionales con total de envíos y montos
        $entregas = RegistroEntrega::where('usuario_id', $usuario->id)
            ->where('nacional?', true) // Corregido: antes estaba 'nacional?'
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->select(
                'servicio_id',
                DB::raw('COUNT(*) as total_envios'),
                DB::raw('SUM(costo_total) as costo_total')
            )
            ->groupBy('servicio_id')
            ->get();
    
        // Calcular los totales correctamente
        $totalEnvios = $entregas->sum('total_envios'); // SUMAR EL TOTAL REAL
        $totalMontos = $entregas->sum('costo_total');
    
        // Preparar datos para la vista
        $data = [
            'entregas' => $entregas,
            'totalEnvios' => $totalEnvios,
            'totalMontos' => $totalMontos,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'usuario' => $usuario,
        ];
    
        // Generar el PDF usando la vista
        $pdf = Pdf::loadView('pdf.reporte', $data);
    
        // Descargar el PDF
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'entregas_nacionales.pdf'
        );
    }
    
    
    public function exportarExcel()
    {
        if ($this->entregas->isEmpty()) {
            session()->flash('mensaje', 'No hay datos para exportar.');
            return;
        }

        return Excel::download(new EntregasExport(
            $this->entregas,
            $this->desde,
            $this->hasta,
            $this->nombre
        ), 'reporte_entregas.xlsx');
    }
}
