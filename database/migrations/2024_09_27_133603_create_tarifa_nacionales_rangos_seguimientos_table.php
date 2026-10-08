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
        Schema::create('tarifa_nacionales_rangos_seguimientos', function (Blueprint $table) {
            $table->id(); 
            $table->integer('tarifa_nacional_rango_seguimiento_id');
            $table->integer('usuario_seguimiento_id');
            $table->integer('tarifa_nacional_rango_id');
            $table->foreign('tarifa_nacional_rango_id')->references('tarifa_nacional_rango_id')->on('tarifa_nacionales_rangos');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifa_nacionales_rangos_seguimientos');
    }
};
