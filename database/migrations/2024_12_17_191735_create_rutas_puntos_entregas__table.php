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
        Schema::create('rutas_puntos_entregas', function (Blueprint $table) {
            $table->id('rutas_puntos_entregas');
            $table->unsignedBigInteger('ruta_id'); // Cambiar a unsignedBigInteger
            $table->foreign('ruta_id')->references('ruta_id')->on('rutas');
            $table->unsignedBigInteger('oficina_id'); // Cambiar a unsignedBigInteger
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas_puntos_entregas');
    }
};
