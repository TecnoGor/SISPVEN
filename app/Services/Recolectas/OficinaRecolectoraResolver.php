<?php

namespace App\Services\Recolectas;

use App\Models\Oficina;

/**
 * Resuelve qué oficina atiende una recolecta según el punto de recolecta:
 * 1º oficina con el mismo municipio y la misma parroquia;
 * 2º (fallback) oficina con el mismo municipio;
 * si tampoco hay, devuelve null y el caller rechaza la solicitud.
 *
 * Solo se consideran oficinas propias (externa = false) y activas
 * (estatus_id = 1, catálogo EstatusOficinasSeeder). Desempate: primero
 * las que están operando hoy (operaciones = true) y luego menor id.
 */
class OficinaRecolectoraResolver
{
    private const ESTATUS_ACTIVA = 1;

    public function resolver(int $municipioId, int $parroquiaId): ?Oficina
    {
        $oficina = $this->consultaBase()
            ->where('municipio_id', $municipioId)
            ->where('parroquia_id', $parroquiaId)
            ->first();

        if ($oficina) {
            return $oficina;
        }

        return $this->consultaBase()
            ->where('municipio_id', $municipioId)
            ->first();
    }

    private function consultaBase()
    {
        return Oficina::query()
            ->where('externa', false)
            ->where('estatus_id', self::ESTATUS_ACTIVA)
            ->orderByDesc('operaciones')
            ->orderBy('oficina_id');
    }
}
