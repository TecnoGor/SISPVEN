<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('numeros_despacho_oficina', function (Blueprint $table) {
            $table->unsignedBigInteger('oficina_destino_id')->nullable();
            $table->foreign('oficina_destino_id')->references('oficina_id')->on('oficinas');
        });

        // El correlativo del despacho es numérico: usarlo como string obligaba a
        // CAST(... AS INTEGER) para ordenar/contar correctamente. Pasarlo a integer
        // elimina esa clase de errores. USING explícito porque Postgres no convierte
        // text -> integer de forma implícita en ALTER COLUMN TYPE.
        DB::statement('ALTER TABLE numeros_despacho_oficina ALTER COLUMN numero_despacho TYPE integer USING (numero_despacho::integer)');

        // Un solo despacho ABIERTO por par (origen, destino) a nivel de base de datos.
        // Blindaje contra condiciones de carrera al crear valijas concurrentes del mismo par.
        DB::statement('CREATE UNIQUE INDEX numeros_despacho_oficina_par_abierto_unique ON numeros_despacho_oficina (oficina_id, oficina_destino_id) WHERE activo = true');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS numeros_despacho_oficina_par_abierto_unique');

        DB::statement('ALTER TABLE numeros_despacho_oficina ALTER COLUMN numero_despacho TYPE varchar(255) USING (numero_despacho::varchar)');

        Schema::table('numeros_despacho_oficina', function (Blueprint $table) {
            $table->dropForeign(['oficina_destino_id']);
            $table->dropColumn('oficina_destino_id');
        });
    }
};
