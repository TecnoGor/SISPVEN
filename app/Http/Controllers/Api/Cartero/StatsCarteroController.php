<?php

namespace App\Http\Controllers\Api\Cartero;

use App\Http\Controllers\Controller;
use App\Models\EnvioEncaminamiento;
use App\Models\IntentoEntrega;
use App\Models\MotivoDevolucion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Estadísticas personales del cartero autenticado.
 */
class StatsCarteroController extends Controller
{
    /** GET /cartero/v1/stats/me?from=YYYY-MM-DD&to=YYYY-MM-DD */
    public function me(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $userId = $request->user()->id;
        $from = $data['from'] ?? now()->startOfMonth()->toDateString();
        $to = $data['to'] ?? now()->toDateString();

        $statusCounts = EnvioEncaminamiento::where('usuario_id', $userId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->whereIn('estatus_id', [16, 17, 18])
            ->groupBy('estatus_id')
            ->select('estatus_id', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'estatus_id');

        $entregadas = (int) ($statusCounts[17] ?? 0);
        $enTransito = (int) ($statusCounts[18] ?? 0);
        $asignadas = (int) ($statusCounts[16] ?? 0);
        $total = $entregadas + $enTransito + $asignadas;
        $exito = $total > 0 ? round($entregadas / $total * 100, 1) : 0;

        $topMotivos = IntentoEntrega::where('usuario_id', $userId)
            ->whereNotNull('motivo_id')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupBy('motivo_id')
            ->select('motivo_id', DB::raw('COUNT(*) as total'))
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $motivosMap = MotivoDevolucion::whereIn('id', $topMotivos->pluck('motivo_id'))
            ->pluck('nombre', 'id');

        return response()->json([
            'range' => ['from' => $from, 'to' => $to],
            'totals' => [
                'assigned' => $asignadas,
                'in_transit' => $enTransito,
                'delivered' => $entregadas,
                'total' => $total,
                'success_rate' => $exito,
            ],
            'top_return_reasons' => $topMotivos->map(fn ($t) => [
                'motivo_id' => (string) $t->motivo_id,
                'nombre' => $motivosMap[$t->motivo_id] ?? '',
                'total' => (int) $t->total,
            ]),
        ]);
    }
}
