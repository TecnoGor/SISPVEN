<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega la columna respuesta_comunicado_id a la tabla comunicados.
 *
 * Contexto: esta columna fue definida dentro de la migracion original
 * create_comunicados_table DESPUES de que dicha migracion ya se habia ejecutado
 * en algunos entornos (produccion). Como Laravel no re-ejecuta migraciones ya
 * aplicadas, esos entornos quedaron sin la columna. Esta migracion nueva la
 * agrega de forma segura.
 *
 * Es idempotente: si la columna ya existe (entornos donde la BD se recreo con la
 * version actualizada de create_comunicados_table) no hace nada, evitando romper
 * esos entornos. Asi el comportamiento es identico en todos lados.
 */
return new class extends Migration {
    public function up(): void
    {
        // Solo agregar la columna si aun no existe (protege los entornos que ya la tienen).
        if (! Schema::hasColumn('comunicados', 'respuesta_comunicado_id')) {
            Schema::table('comunicados', function (Blueprint $table) {
                // Mismo tipo y FK que definia la migracion original: nullable, con
                // FK autorreferencial a comunicados.comunicado_id y onDelete set null.
                $table->unsignedBigInteger('respuesta_comunicado_id')->nullable()->after('remitente_id');
                $table->foreign('respuesta_comunicado_id')
                      ->references('comunicado_id')
                      ->on('comunicados')
                      ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        // Solo revertir si la columna existe, para no fallar en entornos donde
        // esta migracion no llego a agregarla.
        if (Schema::hasColumn('comunicados', 'respuesta_comunicado_id')) {
            Schema::table('comunicados', function (Blueprint $table) {
                $table->dropForeign(['respuesta_comunicado_id']);
                $table->dropColumn('respuesta_comunicado_id');
            });
        }
    }
};
