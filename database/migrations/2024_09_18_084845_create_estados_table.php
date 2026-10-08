<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->id('estado_id');
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('pais_id');
            $table->string('nombre');
            $table->string('codigo')->nullable();
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('pais_id')->references('pais_id')->on('paises');
            $table->foreign('region_id')->references('region_id')->on('regiones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estados', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['region_id']);
            $table->dropForeign(['pais_id']);
        });

        Schema::dropIfExists('estados');
    }
};
