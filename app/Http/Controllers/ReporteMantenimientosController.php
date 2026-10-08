<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MantenimientosVehiculoExport;

class ReporteMantenimientosController extends Controller
{
    public function pdf(Request $request)
    {
        $vehiculo = json_decode(utf8_decode(base64_decode($request->vehiculo)), true);
        $mantenimientos = json_decode(utf8_decode(base64_decode($request->mantenimientos)), true);

        // Validación robusta
        if (!$vehiculo || !isset($vehiculo['placa'])) {
            return response('Vehículo no válido', 400);
        }
        if (!$mantenimientos || !is_array($mantenimientos)) {
            return response('Mantenimientos no válidos', 400);
        }

        $vehiculo = (object) $vehiculo;
        $mantenimientos = collect($mantenimientos)->map(function ($m) { return (object) $m; });

        $pdf = Pdf::loadView('pdf.mantenimientos', [
            'vehiculo' => $vehiculo,
            'mantenimientos' => $mantenimientos,
        ])->setPaper('a4', 'portrait');

        $placa = $vehiculo->placa ?? 'vehiculo';
        return $pdf->download('Mantenimientos_' . $placa . '.pdf');
    }

    public function excel(Request $request)
    {
        $vehiculo = json_decode(utf8_decode(base64_decode($request->vehiculo)), true);
        $mantenimientos = json_decode(utf8_decode(base64_decode($request->mantenimientos)), true);

        if (!$vehiculo || !isset($vehiculo['placa'])) {
            return response('Vehículo no válido', 400);
        }
        if (!$mantenimientos || !is_array($mantenimientos)) {
            return response('Mantenimientos no válidos', 400);
        }

        $vehiculo = (object) $vehiculo;
        $mantenimientos = collect($mantenimientos)->map(function ($m) { return (object) $m; });

        $placa = $vehiculo->placa ?? 'vehiculo';
        return Excel::download(
            new MantenimientosVehiculoExport($vehiculo, $mantenimientos),
            'Mantenimientos_' . $placa . '.xlsx'
        );
    }

    // NUEVO: Exportar PDF con filtros desde la URL
    public function pdfConFiltros(Request $request, $vehiculoId)
    {
        $vehiculo = \App\Models\Vehiculo::with('oficina')->findOrFail($vehiculoId);
        $query = \App\Models\Mantenimiento::with('detalles.servicioFlota')
            ->where('vehiculo_id', $vehiculoId);
        if ($request->has('search') && $request->search) {
            $query->where('descripcion', 'like', '%' . $request->search . '%');
        }
        if ($request->has('start') && $request->start) {
            $query->whereDate('created_at', '>=', $request->start);
        }
        if ($request->has('end') && $request->end) {
            $query->whereDate('created_at', '<=', $request->end);
        }
        $query->orderByDesc('created_at');
        $page = $request->get('page', 1);
        $mantenimientos = $query->paginate(10, ['*'], 'page', $page);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.mantenimientos', [
            'vehiculo' => $vehiculo,
            'mantenimientos' => $mantenimientos,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Mantenimientos_' . ($vehiculo->placa ?? 'vehiculo') . '.pdf');
    }

    // NUEVO: Exportar Excel con filtros desde la URL
    public function excelConFiltros(Request $request, $vehiculoId)
    {
        $vehiculo = \App\Models\Vehiculo::with('oficina')->findOrFail($vehiculoId);
        $query = \App\Models\Mantenimiento::with('detalles.servicioFlota')
            ->where('vehiculo_id', $vehiculoId);
        if ($request->has('search') && $request->search) {
            $query->where('descripcion', 'like', '%' . $request->search . '%');
        }
        if ($request->has('start') && $request->start) {
            $query->whereDate('created_at', '>=', $request->start);
        }
        if ($request->has('end') && $request->end) {
            $query->whereDate('created_at', '<=', $request->end);
        }
        $query->orderByDesc('created_at');
        $page = $request->get('page', 1);
        $mantenimientos = $query->paginate(10, ['*'], 'page', $page);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MantenimientosVehiculoExport($vehiculo, $mantenimientos),
            'Mantenimientos_' . ($vehiculo->placa ?? 'vehiculo') . '.xlsx'
        );
    }
} 