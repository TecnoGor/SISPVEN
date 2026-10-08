<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\CargaCombustible;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ConsumoCombustibleApiController extends Controller
{
    public function index(Request $request)
    {
        // Obtener usuario autenticado
        $usuario = Auth::user();
        
        if (!$usuario) {
            return response()->json([
                'labels' => [],
                'litros' => [],
                'costos' => [],
                'km' => [],
                'totLitros' => 0,
                'totCostos' => 0,
                'mesActual' => Carbon::now()->format('F Y'),
                'totalVehiculos' => 0,
                'error' => 'Usuario no autenticado'
            ]);
        }
        
        // Construir query base para vehículos
        $vehiculosQuery = Vehiculo::whereNotNull('oficina_id');
        
        // Aplicar filtros según el rol del usuario
        // Por ahora, usar una lógica simplificada basada en el ID del usuario
        if ($usuario->id == 1) {
            // SuperAdmin (ID 1) ve todos los vehículos de todas las oficinas
            // No aplicar filtro adicional
        } else {
            // Otros usuarios ven solo vehículos de su oficina
            $oficinaId = $usuario->oficina_id;
            if ($oficinaId) {
                $vehiculosQuery->where('oficina_id', $oficinaId);
            } else {
                // Si no tiene oficina asignada, no mostrar datos
                return response()->json([
                    'labels' => [],
                    'litros' => [],
                    'costos' => [],
                    'km' => [],
                    'totLitros' => 0,
                    'totCostos' => 0,
                    'mesActual' => Carbon::now()->format('F Y'),
                    'totalVehiculos' => 0,
                    'error' => 'Usuario sin oficina asignada'
                ]);
            }
        }
        
        // Obtener vehículos filtrados
        $vehiculos = $vehiculosQuery->get();
        
        // Filtrar cargas de combustible del mes en curso
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $labels = [];
        $litros = [];
        $costos = [];
        $km = [];
        $totLitros = 0;
        $totCostos = 0;
        
        foreach ($vehiculos as $vehiculo) {
            // Obtener cargas de combustible del mes actual para este vehículo
            $cargas = CargaCombustible::where('vehiculo_id', $vehiculo->vehiculo_id)
                ->whereBetween('fecha', [$inicioMes, $finMes])
                ->get();
                
            $sumaLitros = $cargas->sum('litros');
            $sumaCostos = $cargas->sum('costo_total');
            $sumaKm = $cargas->sum('kilometraje');
            
            // Solo incluir vehículos que tengan datos de consumo
            if ($sumaLitros > 0 || $sumaCostos > 0) {
                $labels[] = $vehiculo->placa;
                $litros[] = round($sumaLitros, 2);
                $costos[] = round($sumaCostos, 2);
                $km[] = $sumaKm;
                
                $totLitros += $sumaLitros;
                $totCostos += $sumaCostos;
            }
        }
        
        // Si no hay datos del mes actual, mostrar datos históricos (últimos 3 meses)
        if ($totLitros == 0 && $totCostos == 0) {
            $inicioHistorico = Carbon::now()->subMonths(3)->startOfMonth();
            $finHistorico = Carbon::now()->endOfMonth();
            
            $labels = [];
            $litros = [];
            $costos = [];
            $km = [];
            $totLitros = 0;
            $totCostos = 0;
            
            foreach ($vehiculos as $vehiculo) {
                $cargas = CargaCombustible::where('vehiculo_id', $vehiculo->vehiculo_id)
                    ->whereBetween('fecha', [$inicioHistorico, $finHistorico])
                    ->get();
                    
                $sumaLitros = $cargas->sum('litros');
                $sumaCostos = $cargas->sum('costo_total');
                $sumaKm = $cargas->sum('kilometraje');
                
                if ($sumaLitros > 0 || $sumaCostos > 0) {
                    $labels[] = $vehiculo->placa;
                    $litros[] = round($sumaLitros, 2);
                    $costos[] = round($sumaCostos, 2);
                    $km[] = $sumaKm;
                    
                    $totLitros += $sumaLitros;
                    $totCostos += $sumaCostos;
                }
            }
            
            $mesActual = 'Últimos 3 meses';
        } else {
            $mesActual = Carbon::now()->format('F Y');
        }
        
        return response()->json([
            'labels' => $labels,
            'litros' => $litros,
            'costos' => $costos,
            'km' => $km,
            'totLitros' => round($totLitros, 2),
            'totCostos' => round($totCostos, 2),
            'mesActual' => $mesActual,
            'totalVehiculos' => count($labels),
            'oficinaUsuario' => $this->getOficinaUsuario($usuario),
            'debug' => [
                'usuario_id' => $usuario->id,
                'usuario_nombre' => $usuario->name,
                'oficina_id' => $usuario->oficina_id,
                'vehiculos_count' => $vehiculos->count(),
                'inicio_mes' => $inicioMes->format('Y-m-d'),
                'fin_mes' => $finMes->format('Y-m-d'),
                'es_superadmin' => ($usuario->id == 1),
                'cargas_mes_actual' => CargaCombustible::whereBetween('fecha', [$inicioMes, $finMes])->count(),
                'cargas_totales' => CargaCombustible::count(),
                'labels_count' => count($labels),
                'litros_count' => count($litros),
                'costos_count' => count($costos)
            ]
        ]);
    }
    
    /**
     * Método de prueba para verificar que la API funciona
     */
    public function test()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'API funcionando correctamente',
            'timestamp' => now(),
            'data' => [
                'vehiculos_total' => Vehiculo::whereNotNull('oficina_id')->count(),
                'cargas_total' => CargaCombustible::count(),
                'cargas_mes' => CargaCombustible::whereBetween('fecha', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])->count()
            ]
        ]);
    }
    
    /**
     * Obtiene información de la oficina del usuario para mostrar en las gráficas
     */
    private function getOficinaUsuario($usuario)
    {
        // SuperAdmin ve todas las oficinas
        if ($usuario->id == 1) {
            return 'Todas las oficinas';
        }
        
        $oficinaId = $usuario->oficina_id;
        
        if (!$oficinaId) {
            return 'Sin oficina asignada';
        }
        
        $oficina = \App\Models\Oficina::find($oficinaId);
        return $oficina ? $oficina->nombre : 'Oficina';
    }
} 