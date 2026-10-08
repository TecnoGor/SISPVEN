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
        Schema::create('tarifas_nacionales_conceptos_seguimientos', function (Blueprint $table) {
            $table->id(); // Si deseas un ID auto incremental
            $table->integer('tarifa_nacional_concepto_seguimiento_id');
            $table->integer('usuario_seguimiento_id');
            $table->integer('tarifa_conceptos_id');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas_nacionales_conceptos_seguimientos');
    }
};
