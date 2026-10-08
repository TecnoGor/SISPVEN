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
        Schema::create('facturacion_destinatario', function (Blueprint $table) {
            $table->id('facturacion_destinatario_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('envio_id');
            $table->unsignedBigInteger('registro_entrega_id');
            $table->string('nombre')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->integer('documento')->nullable();
            $table->unsignedBigInteger('tipo_pago_id');
            $table->float('iva');
            $table->float('monto');
            $table->timestamps();

            $table->foreign('tipo_pago_id')->references('tipo_pago_id')->on('tipos_pagos');
            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('registro_entrega_id')->references('registro_entrega_id')->on('registros_entregas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturacion_destinatario', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['envio_id']);
            $table->dropForeign(['tipo_pago_id']);
            $table->dropForeign(['registro_entrega_id']);
        });

        Schema::dropIfExists('facturacion_destinatario');
    }
};
