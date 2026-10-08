<?php

namespace App\Http\Controllers\Api\Cartero;

use App\Http\Controllers\Controller;
use App\Models\CarteroDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Registro idempotente de dispositivos para FCM.
 */
class DeviceCarteroController extends Controller
{
    /** POST /cartero/v1/devices/register */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fcm_token' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'in:android,ios'],
            'app_version' => ['nullable', 'string', 'max:30'],
        ]);

        $device = CarteroDevice::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'fcm_token' => $data['fcm_token'],
            ],
            [
                'platform' => $data['platform'],
                'app_version' => $data['app_version'] ?? null,
                'last_seen' => now(),
            ]
        );

        return response()->json([
            'id' => (string) $device->id,
            'platform' => $device->platform,
            'last_seen' => optional($device->last_seen)->toIso8601String(),
        ]);
    }
}
