<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignKeyDefinition;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id('ciudad_id');
            $table->string('nombre');
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('municipio_id');
            $table->timestamps();

            $table->foreign('estado_id')->references('estado_id')->on('estados');
            $table->foreign('municipio_id')->references('municipio_id')->on('municipios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ciudades', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['estado_id']);
            $table->dropForeign(['municipio_id']);
        });

        Schema::dropIfExists('ciudades');
    }
};
