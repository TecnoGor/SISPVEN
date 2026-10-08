<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Indice UNIQUE PARCIAL sobre los codigos de envio que AUTOGENERA el sistema
     * (EnviosLotes e Iposplus), en sus dos formatos:
     *
     *   origen+destino+correlativo -> OP010CP005000000826
     *   sin oficina destino        -> OP030000000001SO
     *
     * Es parcial a proposito: los codigos de tracking internacional (RE886021191ES),
     * que vienen de fuentes externas, arrastran ~1.052 grupos duplicados historicos y
     * quedan fuera del indice. Aqui solo se blinda lo que genera el sistema.
     *
     * Los tres codigos excluidos son duplicados historicos de envios reales y distintos
     * (distinto remitente/peso/fecha), varios ya entregados al cliente y facturados,
     * por lo que no se renombran ni se borran.
     */
    public function up(): void
    {
        DB::statement("
            CREATE UNIQUE INDEX envios_codigo_envio_autogenerado_unique
            ON envios (codigo_envio)
            WHERE (
                codigo_envio ~ '^(OP|CP|CO|CI|EX)[0-9]{3}(OP|CP|CO|CI|EX)[0-9]{3}[0-9]{9}$'
                OR codigo_envio ~ '^(OP|CP|CO|CI|EX)[0-9]{3}[0-9]{9}SO$'
            )
            AND codigo_envio NOT IN (
                'OP030CP017000000001',
                'OP030CP017000000002',
                'OP092CP001000000001'
            )
        ");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS envios_codigo_envio_autogenerado_unique');
    }
};