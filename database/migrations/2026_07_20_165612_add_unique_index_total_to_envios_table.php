<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // UNIQUE total case-insensitive sobre codigo_envio. Los NULL quedan permitidos
        // (Postgres no los considera iguales). Requiere la tabla ya saneada.
        DB::statement('CREATE UNIQUE INDEX envios_codigo_envio_unique ON envios (UPPER(codigo_envio))');

        // El índice parcial de autogenerados queda subsumido por este total.
        DB::statement('DROP INDEX IF EXISTS envios_codigo_envio_autogenerado_unique');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS envios_codigo_envio_unique');

        // Restaurar el índice parcial que existía antes.
        DB::statement("
            CREATE UNIQUE INDEX envios_codigo_envio_autogenerado_unique
            ON envios (codigo_envio)
            WHERE (
                codigo_envio ~ '^(OP|CP|CO|CI|EX)[0-9]{3}(OP|CP|CO|CI|EX)[0-9]{3}[0-9]{9}\$'
                OR codigo_envio ~ '^(OP|CP|CO|CI|EX)[0-9]{3}[0-9]{9}SO\$'
            )
            AND codigo_envio NOT IN (
                'OP030CP017000000001',
                'OP030CP017000000002',
                'OP092CP001000000001'
            )
        ");
    }
};
