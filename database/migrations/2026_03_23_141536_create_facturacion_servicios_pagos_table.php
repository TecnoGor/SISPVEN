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
        Schema::create('facturacion_servicios_pagos', function (Blueprint $table) {
            $table->id('facturacion_servicios_pagos_id');
            $table->unsignedBigInteger('facturacion_servicio_id');
            $table->unsignedBigInteger('tipo_pago_id');
            $table->decimal('monto', 10, 2);
            $table->string('referencia_bancaria')->nullable();
            $table->timestamps();

            $table->foreign('facturacion_servicio_id')->references('facturacion_servicio_id')->on('facturacion_servicios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturacion_servicios_pagos');
    }
};
