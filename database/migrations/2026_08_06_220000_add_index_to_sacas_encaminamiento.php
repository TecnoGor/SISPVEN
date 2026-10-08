<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PostgreSQL NO indexa automaticamente las claves foraneas (a diferencia de
     * MySQL), asi que `sacas_encaminamiento.saca_id` solo tenia el indice de la PK.
     *
     * La resolucion de ubicacion de una valija busca su ultimo movimiento
     * (filtrar por saca_id + tomar el mayor saca_encaminamiento_id). El indice
     * compuesto cubre ambas operaciones: el filtro y el orden, evitando el scan
     * secuencial de la tabla por cada valija del listado.
     */
    public function up(): void
    {
        Schema::table('sacas_encaminamiento', function (Blueprint $table) {
            $table->index(['saca_id', 'saca_encaminamiento_id'], 'sacas_encaminamiento_saca_ultimo_idx');
        });
    }

    public function down(): void
    {
        Schema::table('sacas_encaminamiento', function (Blueprint $table) {
            $table->dropIndex('sacas_encaminamiento_saca_ultimo_idx');
        });
    }
};
