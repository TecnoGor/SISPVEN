<?php

namespace App\Http\Controllers\Api\Cartero;

use App\Http\Controllers\Controller;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioEvidencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Lista la ruta diaria del cartero autenticado.
 *
 * Un envío está en ruta activa cuando:
 *   - tiene una fila en asignacion_envio_cartero con user_id = autenticado y estatus = true.
 *   - su último envios_encaminamiento tiene estatus_id IN (16, 18) o no tiene aún.
 *
 * Estatus considerados ruta activa:
 *   16 → Asignado a Repartidor
 *   18 → Recibido por Repartidor (en tránsito)
 */
class RouteCarteroController extends Controller
{
    private const RUTA_ACTIVA = [16, 18];

    /** GET /cartero/v1/routes/daily?date=YYYY-MM-DD */
    public function daily(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $userId = $request->user()->id;
        $date = $request->query('date');

        $query = EnvioAlmacen::query()
            ->with(['envio', 'oficina'])
            ->whereHas('carteros', function ($q) use ($userId) {
                $q->where('users.id', $userId)
                  ->where('asignacion_envio_cartero.estatus', true);
            });

        if ($date) {
            $query->whereDate('updated_at', $date);
        }

        $items = $query->orderBy('updated_at')->get();

        // Filtrar por último estatus de encaminamiento.
        $envioIds = $items->pluck('envio_id')->filter()->unique()->all();
        $ultimoEstatusPorEnvio = $this->ultimoEstatusPorEnvio($envioIds);

        $items = $items->filter(function ($a) use ($ultimoEstatusPorEnvio) {
            $estatus = $ultimoEstatusPorEnvio[$a->envio_id] ?? null;
            // Sin encaminamiento aún o estatus en ruta activa.
            return $estatus === null || in_array($estatus, self::RUTA_ACTIVA, true);
        });

        $evidencias = EnvioEvidencia::whereIn('envio_id', $envioIds)
            ->orderBy('capturada_en', 'desc')
            ->get()
            ->groupBy('envio_id');

        $userId = (string) $request->user()->id;

        $payload = $items->map(function ($a) use ($evidencias, $ultimoEstatusPorEnvio, $userId) {
            $envio = $a->envio;
            $tracking = $envio?->codigo_envio ?? $a->codigo ?? '';
            $evidenceIds = ($evidencias[$a->envio_id] ?? collect())
                ->pluck('evidencia_id')
                ->map(fn ($v) => (string) $v)
                ->all();
            $estatus = $ultimoEstatusPorEnvio[$a->envio_id] ?? 16;

            return [
                'id' => (string) $a->envio_almacen_id,
                'tracking_number' => $tracking,
                'recipient_name' => trim(($envio?->nombre_dest ?? '') . ' ' . ($envio?->apellido_dest ?? '')),
                'recipient_document' => $envio?->documento_dest ?? '',
                'recipient_phone' => $envio?->tlf_dest ?? '',
                'address' => [
                    'street' => $envio?->direccion_dest ?? '',
                    'city' => $envio?->ciudad_dest ?? '',
                    'state' => $envio?->estado_dest ?? '',
                    'zip_code' => $envio?->codigo_postal_dest ?? '',
                    'reference' => null,
                    'latitude' => null,
                    'longitude' => null,
                ],
                'status' => $this->mapStatus($estatus),
                'scheduled_date' => optional($a->updated_at)->toIso8601String(),
                'route_order' => 0,
                'courier_id' => $userId,
                'evidence_ids' => $evidenceIds,
            ];
        })->values();

        return response()->json(['data' => $payload]);
    }

    /**
     * Devuelve [envio_id => ultimo_estatus_id] para los envíos pedidos.
     */
    private function ultimoEstatusPorEnvio(array $envioIds): array
    {
        if (empty($envioIds)) return [];

        return EnvioEncaminamiento::query()
            ->whereIn('envio_id', $envioIds)
            ->orderBy('created_at', 'desc')
            ->get(['envio_id', 'estatus_id', 'created_at'])
            ->groupBy('envio_id')
            ->map(fn ($rows) => (int) $rows->first()->estatus_id)
            ->all();
    }

    private function mapStatus(?int $estatus): string
    {
        return match ($estatus) {
            16 => 'pending',
            18 => 'inTransit',
            17 => 'delivered',
            default => 'pending',
        };
    }
}
