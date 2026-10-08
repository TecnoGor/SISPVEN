<?php

namespace App\Http\Controllers\Api\Clientes;

use App\Http\Controllers\Controller;
use App\Models\Recolecta;
use App\Models\RecolectaEstatus;
use App\Models\RecolectaPago;
use App\Models\TipoPago;
use App\Services\Recolectas\RecolectaFlowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Registro del pago por adelantado de una recolecta.
 *
 * SOLO ESTRUCTURA: el cliente reporta el pago (método, monto, referencia)
 * y la recolecta pasa a 'pago_reportado'. La verificación del pago está en
 * STAND-BY: hoy la confirma manualmente la oficina desde la pantalla web
 * (RecolectaFlowService::confirmarPago); una verificación automática futura
 * llamará ese mismo hook.
 */
class PagoRecolectaClienteController extends Controller
{
    /**
     * ids de tipos_pagos que exigen número de referencia
     * (3 = Transferencia Bancaria, 4 = Pago Móvil — patrón CajaDePago).
     */
    private const TIPOS_CON_REFERENCIA = [3, 4];

    public function __construct(
        private RecolectaFlowService $flow,
    ) {}

    /** POST /clientes/v1/recolectas/{id}/pago */
    public function store(Request $request, int $id): JsonResponse
    {
        $recolecta = Recolecta::deUsuarioApp($request->user()->usuario_id)
            ->where('recolecta_id', $id)
            ->with('estatus')
            ->firstOrFail();

        if (!$recolecta->tieneEstatus(RecolectaEstatus::SOLICITADA)) {
            return response()->json([
                'message' => 'La recolecta no admite registrar un pago en su estado actual.',
            ], 422);
        }

        $data = $request->validate([
            'tipo_pago_id' => 'required|integer|exists:tipos_pagos,tipo_pago_id',
            'monto' => 'required|numeric|min:0.01',
            'numero_referencia' => 'nullable|string|max:40',
            'banco_codigo' => 'nullable|string|max:10',
            'telefono_pagador' => 'nullable|string|max:15',
            'fecha_pago' => 'nullable|date',
        ]);

        $tipoPago = TipoPago::find($data['tipo_pago_id']);
        if (!$tipoPago->activo) {
            return response()->json(['message' => 'El método de pago no está disponible.'], 422);
        }

        if (in_array((int) $data['tipo_pago_id'], self::TIPOS_CON_REFERENCIA, true) && empty($data['numero_referencia'])) {
            return response()->json([
                'message' => 'El número de referencia es obligatorio para este método de pago.',
            ], 422);
        }

        if (round((float) $data['monto'], 2) < round((float) $recolecta->total, 2)) {
            return response()->json([
                'message' => 'El monto reportado no cubre el total de la recolecta.',
            ], 422);
        }

        $pago = DB::transaction(function () use ($recolecta, $data) {
            $pago = RecolectaPago::create([
                'recolecta_id' => $recolecta->recolecta_id,
                'tipo_pago_id' => $data['tipo_pago_id'],
                'monto' => $data['monto'],
                'numero_referencia' => $data['numero_referencia'] ?? null,
                'banco_codigo' => $data['banco_codigo'] ?? null,
                'telefono_pagador' => $data['telefono_pagador'] ?? null,
                'fecha_pago' => $data['fecha_pago'] ?? null,
                'estatus' => RecolectaPago::ESTATUS_REPORTADO,
            ]);

            $this->flow->transicionar($recolecta, RecolectaEstatus::PAGO_REPORTADO);

            return $pago;
        });

        return response()->json([
            'data' => [
                'id' => (string) $pago->recolecta_pago_id,
                'estatus' => $pago->estatus,
                'recolecta_estatus' => $recolecta->estatus->slug,
                'monto' => (string) $pago->monto,
            ],
        ], 201);
    }
}
