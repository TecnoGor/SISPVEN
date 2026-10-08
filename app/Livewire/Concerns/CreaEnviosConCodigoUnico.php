<?php

namespace App\Livewire\Concerns;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Creación de envíos con código autogenerado a prueba de lotes concurrentes.
 *
 * El correlativo se calcula leyendo el último código de la BD, así que dos lotes que se
 * registran a la vez hacia el mismo destino generan el mismo código: no se ven entre sí
 * hasta el COMMIT. El índice único parcial sobre los códigos autogenerados
 * (envios_codigo_envio_autogenerado_unique) es lo único que corta esa carrera; aquí se
 * atiende el rebote regenerando el código, que releerá el nuevo último.
 */
trait CreaEnviosConCodigoUnico
{
    protected int $maxIntentosCodigo = 5;

    /**
     * @param  callable  $resolverCodigo  Devuelve el código a usar; se reevalúa en cada intento.
     * @param  callable  $construirEnvio  Recibe el código resuelto y crea el Envio.
     */
    protected function crearEnvioConCodigoUnico(callable $resolverCodigo, callable $construirEnvio)
    {
        for ($intento = 1; $intento <= $this->maxIntentosCodigo; $intento++) {
            try {
                // Transacción anidada = SAVEPOINT: el rebote revierte solo este intento y
                // deja viva la transacción del lote completo.
                return DB::transaction(fn() => $construirEnvio($resolverCodigo()));
            } catch (UniqueConstraintViolationException $e) {
                if ($intento === $this->maxIntentosCodigo) {
                    throw $e;
                }
            }
        }
    }
}
