<?php

namespace App\Services;

use App\Models\NumeroDespachoOficina;

class DespachoService
{
    /**
     * Resuelve el numero_despacho_id ACTIVO para un par (origen, destino).
     *
     * El correlativo del despacho es por par origen->destino: todas las valijas
     * creadas (o reasignadas) hacia ese par comparten el mismo despacho ABIERTO
     * hasta que se le da salida.
     *
     * - Si ya existe un despacho activo para el par, lo devuelve (la valija se suma).
     * - Si no, crea el siguiente correlativo del par (max historico + 1, o 1).
     *
     * IMPORTANTE: debe llamarse dentro de una transacción; usa lockForUpdate sobre
     * el despacho activo para serializar creaciones concurrentes del mismo par.
     *
     * Los despachos con oficina_destino_id NULL (datos históricos sin migrar) se
     * ignoran deliberadamente: no pertenecen a ningún par bien formado y no deben
     * capturar valijas nuevas ni influir en el correlativo.
     * Ver docs/notas/problematica-despachos-destino-null.md
     *
     * @param  int  $oficinaOrigenId
     * @param  int  $oficinaDestinoId
     * @return int  numero_despacho_id a asignar a la valija
     */
    public function resolverDespachoActivo(int $oficinaOrigenId, int $oficinaDestinoId): int
    {
        $despachoActivo = NumeroDespachoOficina::where('oficina_id', $oficinaOrigenId)
            ->where('oficina_destino_id', $oficinaDestinoId)
            ->where('activo', true)
            ->lockForUpdate()
            ->first();

        if ($despachoActivo) {
            return $despachoActivo->numero_despacho_id;
        }

        // No hay despacho abierto: el correlativo avanza tomando el maximo historico
        // del par (numero_despacho ya es integer, sin CAST).
        // Sin lockForUpdate: Postgres no permite FOR UPDATE con agregaciones (MAX),
        // y la concurrencia ya esta cubierta por el lock del despacho activo de arriba
        // y por el indice unico parcial (oficina_id, oficina_destino_id) WHERE activo.
        $ultimoNumero = NumeroDespachoOficina::where('oficina_id', $oficinaOrigenId)
            ->where('oficina_destino_id', $oficinaDestinoId)
            ->max('numero_despacho');

        $nuevoNumero = $ultimoNumero ? $ultimoNumero + 1 : 1;

        $nuevoDespacho = NumeroDespachoOficina::create([
            'oficina_id'         => $oficinaOrigenId,
            'oficina_destino_id' => $oficinaDestinoId,
            'numero_despacho'    => $nuevoNumero,
            'activo'             => true,
        ]);

        return $nuevoDespacho->numero_despacho_id;
    }
}
