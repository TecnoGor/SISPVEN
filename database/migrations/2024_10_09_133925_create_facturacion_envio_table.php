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
        Schema::create('facturacion_envio', function (Blueprint $table) {
            $table->id('facturacion_envio_id');
            $table->unsignedBigInteger('facturacion_id');
            $table->unsignedBigInteger('envio_id')->nullable();
            $table->unsignedBigInteger('envio_exporta_facil_id')->nullable();
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('servicio_id');
            $table->timestamps();

            $table->foreign('facturacion_id')->references('facturacion_id')->on('facturaciones');
            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('envio_exporta_facil_id')->references('envio_exporta_facil_id')->on('envios_exporta_facil');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturacion_pagos', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['facturacion_id']);
            $table->dropForeign(['envio_id']);
            $table->dropForeign(['envio_exporta_facil_id']);
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['servicio_id']);
        });

        Schema::dropIfExists('facturacion_envio');
    }
};
