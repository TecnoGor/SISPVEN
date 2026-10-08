<?php

namespace App\Http\Controllers\Api\Cartero;

use App\Http\Controllers\Controller;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioEvidencia;
use App\Models\IntentoEntrega;
use App\Models\MotivoDevolucion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Detalle de un envío y operaciones operativas asociadas:
 *   - show: detalle.
 *   - updateStatus: cambia estatus (16/17/18) y registra EnvioEncaminamiento.
 *   - final: cierre 7.2/7.3 (efectiva / fallida / devuelta).
 *   - attempt: registra un intento fallido con GPS y motivo opcional.
 */
class DeliveryCarteroController extends Controller
{
    private const FINAL_RESULT_TO_STATUS = [
        'effective' => 17,     // Entregado al Cliente
        'failedAttempt' => 18, // Recibido por Repartidor (sigue en ruta)
        'returned' => 17,      // Cerrado como devuelto
    ];

    /** GET /cartero/v1/deliveries/{id} */
    public function show(Request $request, string $id): JsonResponse
    {
        $a = EnvioAlmacen::with(['envio'])->findOrFail($id);
        $this->ensureBelongsToUser($a, $request->user()->id);

        $envio = $a->envio;
        $evidencias = EnvioEvidencia::where('envio_id', $a->envio_id)->get();
        $estatus = $this->ultimoEstatus($a->envio_id);

        return response()->json([
            'id' => (string) $a->envio_almacen_id,
            'tracking_number' => $envio?->codigo_envio ?? $a->codigo ?? '',
            'recipient' => [
                'name' => trim(($envio?->nombre_dest ?? '') . ' ' . ($envio?->apellido_dest ?? '')),
                'document' => $envio?->documento_dest,
                'phone' => $envio?->tlf_dest,
                'address' => $envio?->direccion_dest,
                'city' => $envio?->ciudad_dest,
                'state' => $envio?->estado_dest,
            ],
            'status' => $estatus,
            'evidences' => $evidencias->map(fn ($e) => [
                'id' => (string) $e->evidencia_id,
                'tipo' => $e->tipo,
                'hash' => $e->hash_sha256,
                'capturada_en' => optional($e->capturada_en)->toIso8601String(),
                'latitud' => $e->latitud,
                'longitud' => $e->longitud,
            ]),
        ]);
    }

    /** PUT /cartero/v1/deliveries/{id}/status */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'integer', 'in:16,17,18'],
            'devolucion' => ['nullable', 'boolean'],
        ]);

        $a = EnvioAlmacen::findOrFail($id);
        $this->ensureBelongsToUser($a, $request->user()->id);

        $enc = DB::transaction(function () use ($a, $data, $request) {
            return EnvioEncaminamiento::create([
                'envio_id' => $a->envio_id,
                'oficina_id' => $a->oficina_id,
                'usuario_id' => $request->user()->id,
                'estatus_id' => $data['status'],
                'devolucion' => $data['devolucion'] ?? false,
            ]);
        });

        return response()->json([
            'id' => (string) $a->envio_almacen_id,
            'status' => $data['status'],
            'updated_at' => $enc->created_at->toIso8601String(),
        ]);
    }

    /** POST /cartero/v1/deliveries/{id}/final */
    public function final(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'final_result' => ['required', 'in:effective,failedAttempt,returned'],
            'return_reason' => ['nullable', 'string'],
            'recipient_data' => ['required', 'array'],
            'recipient_data.first_name' => ['nullable', 'string'],
            'recipient_data.last_name' => ['nullable', 'string'],
            'recipient_data.document' => ['nullable', 'string'],
            'recipient_data.phone' => ['nullable', 'string'],
            'recipient_data.address' => ['nullable', 'string'],
            'evidence_ids' => ['nullable', 'array'],
            'evidence_ids.*' => ['integer'],
        ]);

        $a = EnvioAlmacen::findOrFail($id);
        $this->ensureBelongsToUser($a, $request->user()->id);

        if ($data['final_result'] === 'returned' && empty($data['return_reason'])) {
            return response()->json([
                'message' => 'Debe indicar un motivo de devolución',
                'errors' => ['return_reason' => ['Requerido cuando final_result=returned']],
            ], 422);
        }

        $motivoId = null;
        if (!empty($data['return_reason'])) {
            $motivoId = MotivoDevolucion::where('codigo', $data['return_reason'])
                ->value('id');
        }

        $nuevoEstatus = self::FINAL_RESULT_TO_STATUS[$data['final_result']];
        $esDevolucion = $data['final_result'] === 'returned';

        $enc = DB::transaction(function () use ($a, $request, $nuevoEstatus, $esDevolucion, $motivoId, $data) {
            $encaminamiento = EnvioEncaminamiento::create([
                'envio_id' => $a->envio_id,
                'oficina_id' => $a->oficina_id,
                'usuario_id' => $request->user()->id,
                'estatus_id' => $nuevoEstatus,
                'devolucion' => $esDevolucion,
            ]);

            if ($data['final_result'] === 'failedAttempt' || $esDevolucion) {
                IntentoEntrega::create([
                    'envio_almacen_id' => $a->envio_almacen_id,
                    'envio_id' => $a->envio_id,
                    'usuario_id' => $request->user()->id,
                    'motivo_id' => $motivoId,
                ]);
            }

            return $encaminamiento;
        });

        return response()->json([
            'id' => (string) $a->envio_almacen_id,
            'status' => $nuevoEstatus,
            'final_result' => $data['final_result'],
            'return_reason' => $data['return_reason'] ?? null,
            'updated_at' => $enc->created_at->toIso8601String(),
        ]);
    }

    /** Devuelve el último estatus_id de encaminamiento del envío. */
    private function ultimoEstatus(int $envioId): ?int
    {
        $row = EnvioEncaminamiento::where('envio_id', $envioId)
            ->orderBy('created_at', 'desc')
            ->first(['estatus_id']);
        return $row ? (int) $row->estatus_id : null;
    }

    /** POST /cartero/v1/deliveries/{id}/attempt */
    public function attempt(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'return_reason' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'evidence_id' => ['nullable', 'integer'],
        ]);

        $a = EnvioAlmacen::findOrFail($id);
        $this->ensureBelongsToUser($a, $request->user()->id);

        $motivoId = null;
        if (!empty($data['return_reason'])) {
            $motivoId = MotivoDevolucion::where('codigo', $data['return_reason'])
                ->value('id');
        }

        $intento = IntentoEntrega::create([
            'envio_almacen_id' => $a->envio_almacen_id,
            'envio_id' => $a->envio_id,
            'usuario_id' => $request->user()->id,
            'motivo_id' => $motivoId,
            'latitud' => $data['latitude'] ?? null,
            'longitud' => $data['longitude'] ?? null,
            'evidencia_id' => $data['evidence_id'] ?? null,
        ]);

        $totalIntentos = IntentoEntrega::where('envio_almacen_id', $a->envio_almacen_id)->count();

        return response()->json([
            'intento_entrega_id' => $intento->intento_entrega_id,
            'envio_almacen_id' => (string) $a->envio_almacen_id,
            'attempt_number' => $totalIntentos,
            'suggest_return' => $totalIntentos >= 3,
        ]);
    }

    private function ensureBelongsToUser(EnvioAlmacen $a, int $userId): void
    {
        $belongs = $a->carteros()
            ->where('users.id', $userId)
            ->where('asignacion_envio_cartero.estatus', true)
            ->exists();
        abort_unless($belongs, 403, 'Este envío no está asignado al cartero autenticado');
    }
}
