<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Índices para la pantalla "Envíos por confirmar" (EnviosPorConfirmar::baseQuery).
 *
 * Verificado con EXPLAIN (ANALYZE, BUFFERS) sobre una copia de producción
 * (23.181 envíos / 67.915 encaminamientos / 5.083 facturaciones), filtrando por
 * la oficina más grande (OP010): 46,9 ms y 15.495 buffers antes, 12,6 ms y
 * 13.728 después. Los tres problemas que resuelven:
 *
 *  1. facturacion_envio solo tenía su PK, así que el whereHas('facturacion_envios')
 *     forzaba un Seq Scan completo de la tabla en cada carga de la pantalla.
 *  2. codigo_envio LIKE 'OP010%' no podía usar envios_codigo_envio_index: bajo la
 *     collation Spanish_Venezuela.1252 un btree normal no sirve para búsquedas por
 *     prefijo. De ahí varchar_pattern_ops, que sí las soporta.
 *  3. envios_encaminamiento se accedía por envio_id y filtraba estatus_id contra el
 *     heap, en 2.216 loops por las dos subconsultas (el EXISTS de estatus 1 y el
 *     NOT EXISTS de estatus > 1). El índice compuesto cubre ambas.
 *
 * Con estos índices los dos primeros pasan a Index Only Scan con Heap Fetches: 0.
 *
 * CREATE INDEX CONCURRENTLY no bloquea escrituras mientras construye el índice.
 * No admite transacción, de ahí $withinTransaction = false.
 */
return new class extends Migration
{
    public $withinTransaction = false;

    /** [tabla, nombre del índice, columnas] */
    private array $indices = [
        // whereHas('facturacion_envios'): la tabla no tenía índice por envio_id.
        ['facturacion_envio', 'facturacion_envio_envio_id_index', 'envio_id'],

        // EXISTS estatus_id = 1 y NOT EXISTS estatus_id > 1, ambos por envio_id.
        ['envios_encaminamiento', 'envios_encaminamiento_envio_estatus_index', 'envio_id, estatus_id'],

        // whereIn(servicio_id) + codigo_envio LIKE 'codigo_oficina%'.
        // varchar_pattern_ops es lo que habilita el prefijo bajo collation española.
        ['envios', 'envios_servicio_codigo_index', 'servicio_id, codigo_envio varchar_pattern_ops'],
    ];

    public function up(): void
    {
        foreach ($this->indices as [$tabla, $indice, $columnas]) {
            DB::statement("CREATE INDEX CONCURRENTLY IF NOT EXISTS {$indice} ON {$tabla} ({$columnas})");
        }

        // Actualizar estadísticas para que el planificador use los índices de inmediato.
        foreach (array_unique(array_column($this->indices, 0)) as $tabla) {
            DB::statement("ANALYZE {$tabla}");
        }
    }

    public function down(): void
    {
        foreach ($this->indices as [$tabla, $indice, $columnas]) {
            DB::statement("DROP INDEX CONCURRENTLY IF EXISTS {$indice}");
        }
    }
};
