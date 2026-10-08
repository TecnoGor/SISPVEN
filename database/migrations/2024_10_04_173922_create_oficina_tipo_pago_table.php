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
        Schema::create('oficina_tipo_pago', function (Blueprint $table) {
            $table->id('oficina_tipo_pago_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('tipo_pago_id');

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('tipo_pago_id')->references('tipo_pago_id')->on('tipos_pagos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oficina_tipo_pago', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['tipo_pago_id']);
        });

        Schema::dropIfExists('oficina_tipo_pago');
    }
};
