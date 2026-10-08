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
        Schema::create('respaldo_inventario_diario', function (Blueprint $table) {
            $table->id('respaldo_inventario_diario_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('insumo_id');
            $table->integer('cantidad');
            $table->date('fecha');
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('insumo_id')->references('insumo_id')->on('insumos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respaldo_inventario_diario');
    }
};
