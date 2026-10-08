<?php

namespace App\Http\Controllers\Api\Cartero;

use App\Http\Controllers\Controller;
use App\Models\EnvioAlmacen;
use App\Models\EnvioEvidencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Recibe una evidencia (foto / firma) capturada por la app móvil.
 *
 * Guarda el archivo bajo storage/app/evidencias/{año}/{mes}/{uuid}.{ext}
 * y registra metadatos (hash, transaction_id, GPS) en envio_evidencias.
 */
class EvidenceCarteroController extends Controller
{
    /** POST /cartero/v1/deliveries/{id}/evidence  (multipart) */
    public function upload(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'evidence_file' => ['required', 'file', 'max:10240'], // 10 MB
            'type' => ['required', 'in:photo,signature'],
            'hash' => ['required', 'string', 'size:64'],
            'transaction_id' => ['nullable', 'string', 'max:64'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'gps_accuracy' => ['nullable', 'numeric'],
            'captured_at' => ['nullable', 'date'],
            'courier_id' => ['nullable', 'string'],
            'signature_data' => ['nullable', 'string'],
        ]);

        $a = EnvioAlmacen::findOrFail($id);
        $belongs = $a->carteros()
            ->where('users.id', $request->user()->id)
            ->where('asignacion_envio_cartero.estatus', true)
            ->exists();
        abort_unless($belongs, 403, 'Este envío no está asignado al cartero autenticado');

        $now = now();
        $folder = sprintf('evidencias/%04d/%02d', $now->year, $now->month);
        $ext = $data['type'] === 'signature' ? 'png' : 'jpg';
        $filename = Str::uuid()->toString() . '.' . $ext;

        $path = $request->file('evidence_file')->storeAs($folder, $filename, 'local');

        $evidencia = EnvioEvidencia::create([
            'envio_id' => $a->envio_id,
            'envio_almacen_id' => $a->envio_almacen_id,
            'user_id' => $request->user()->id,
            'tipo' => $data['type'],
            'ruta_archivo' => $path,
            'hash_sha256' => $data['hash'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'latitud' => $data['latitude'] ?? null,
            'longitud' => $data['longitude'] ?? null,
            'precision_gps' => $data['gps_accuracy'] ?? null,
            'capturada_en' => $data['captured_at'] ?? $now,
        ]);

        return response()->json([
            'evidencia_id' => (string) $evidencia->evidencia_id,
            'ruta_archivo' => $path,
            'hash_sha256' => $evidencia->hash_sha256,
            'transaction_id' => $evidencia->transaction_id,
            'capturada_en' => optional($evidencia->capturada_en)->toIso8601String(),
        ], 201);
    }
}
