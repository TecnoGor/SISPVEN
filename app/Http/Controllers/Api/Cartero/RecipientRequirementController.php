<?php

namespace App\Http\Controllers\Api\Cartero;

use App\Http\Controllers\Controller;
use App\Models\RecipientRequirementConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Configuración 7.4: campos obligatorios u opcionales del destinatario.
 */
class RecipientRequirementController extends Controller
{
    private const DEFAULTS = [
        'firstName' => 'Nombre',
        'lastName' => 'Apellido',
        'document' => 'Cédula',
        'phone' => 'Teléfono',
        'address' => 'Dirección',
    ];

    /** GET /cartero/v1/settings/recipient-requirements */
    public function show(): JsonResponse
    {
        $existentes = RecipientRequirementConfig::all()->keyBy('field_key');

        $fields = [];
        foreach (self::DEFAULTS as $key => $label) {
            $row = $existentes->get($key);
            $fields[] = [
                'field_key' => $key,
                'label' => $row?->label ?? $label,
                'is_required' => $row?->is_required ?? true,
                'updated_at' => optional($row?->updated_at)->toIso8601String(),
            ];
        }

        return response()->json(['data' => $fields]);
    }

    /** PUT /cartero/v1/settings/recipient-requirements */
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.field_key' => ['required', 'string', 'max:40'],
            'fields.*.label' => ['required', 'string', 'max:60'],
            'fields.*.is_required' => ['required', 'boolean'],
        ]);

        $now = now();
        foreach ($data['fields'] as $field) {
            RecipientRequirementConfig::updateOrCreate(
                ['field_key' => $field['field_key']],
                [
                    'label' => $field['label'],
                    'is_required' => $field['is_required'],
                    'updated_at' => $now,
                    'updated_by' => $request->user()->id,
                ]
            );
        }

        return $this->show();
    }
}
