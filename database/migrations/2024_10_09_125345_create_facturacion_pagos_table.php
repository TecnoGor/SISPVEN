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
        Schema::create('facturacion_pagos', function (Blueprint $table) {
            $table->id('facturacion_pago_id');
            $table->unsignedBigInteger('facturacion_id');
            $table->unsignedBigInteger('tipo_pago_id');
            $table->decimal('monto');
            $table->timestamps();

            $table->foreign('facturacion_id')->references('facturacion_id')->on('facturaciones');
            $table->foreign('tipo_pago_id')->references('tipo_pago_id')->on('tipos_pagos');
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
            $table->dropForeign(['tipo_pago_id']);
        });

        Schema::dropIfExists('facturacion_pagos');
    }
};
