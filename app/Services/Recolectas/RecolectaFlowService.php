<?php

namespace App\Services\Recolectas;

use App\Models\Recolecta;
use App\Models\RecolectaEstatus;
use App\Models\RecolectaPago;
use App\Models\User;

/**
 * Centraliza el grafo de estados de una recolecta. Toda transición pasa
 * por aquí; los saltos ilegales lanzan TransicionRecolectaInvalidaException.
 *
 * Flujo (pago por adelantado):
 *   solicitada → pago_reportado → pago_confirmado → recolectada
 *   solicitada|pago_reportado → cancelada (cliente)
 *   solicitada|pago_reportado|pago_confirmado → cancelada|rechazada (oficina)
 */
class RecolectaFlowService
{
    private const TRANSICIONES = [
        RecolectaEstatus::SOLICITADA => [
            RecolectaEstatus::PAGO_REPORTADO,
            RecolectaEstatus::CANCELADA,
            RecolectaEstatus::RECHAZADA,
        ],
        RecolectaEstatus::PAGO_REPORTADO => [
            RecolectaEstatus::PAGO_CONFIRMADO,
            RecolectaEstatus::CANCELADA,
            RecolectaEstatus::RECHAZADA,
        ],
        RecolectaEstatus::PAGO_CONFIRMADO => [
            RecolectaEstatus::RECOLECTADA,
            RecolectaEstatus::CANCELADA,
            RecolectaEstatus::RECHAZADA,
        ],
        RecolectaEstatus::RECOLECTADA => [],
        RecolectaEstatus::CANCELADA => [],
        RecolectaEstatus::RECHAZADA => [],
    ];

    /** Estados desde los que el CLIENTE puede cancelar. */
    public const CANCELABLES_POR_CLIENTE = [
        RecolectaEstatus::SOLICITADA,
        RecolectaEstatus::PAGO_REPORTADO,
    ];

    public function transicionar(Recolecta $recolecta, string $slugDestino): Recolecta
    {
        $slugActual = $recolecta->estatus->slug;
        $permitidas = self::TRANSICIONES[$slugActual] ?? [];

        if (!in_array($slugDestino, $permitidas, true)) {
            throw new TransicionRecolectaInvalidaException(
                "Transición inválida de '{$slugActual}' a '{$slugDestino}' para la recolecta {$recolecta->codigo}."
            );
        }

        $recolecta->recolecta_estatus_id = RecolectaEstatus::porSlug($slugDestino)->recolecta_estatus_id;
        $recolecta->save();

        return $recolecta->load('estatus');
    }

    /**
     * Confirma el pago de la recolecta. HOY lo dispara manualmente la
     * oficina desde la pantalla web; cuando exista verificación automática
     * (BDV u otra, en stand-by) deberá llamar a este mismo método.
     */
    public function confirmarPago(Recolecta $recolecta, User $confirmadoPor): Recolecta
    {
        $this->transicionar($recolecta, RecolectaEstatus::PAGO_CONFIRMADO);

        $recolecta->pagos()
            ->where('estatus', RecolectaPago::ESTATUS_REPORTADO)
            ->update([
                'estatus' => RecolectaPago::ESTATUS_CONFIRMADO,
                'confirmado_por' => $confirmadoPor->id,
                'confirmado_en' => now(),
            ]);

        $recolecta->pago_confirmado_en = now();
        $recolecta->pago_confirmado_por = $confirmadoPor->id;
        $recolecta->save();

        return $recolecta;
    }
}
