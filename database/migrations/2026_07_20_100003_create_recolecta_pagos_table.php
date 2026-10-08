<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Pagos reportados por el cliente para una recolecta (pago por
     * adelantado). Solo estructura: la verificación automática está en
     * stand-by; hoy la oficina confirma manualmente.
     */
    public function up(): void
    {
        Schema::create('recolecta_pagos', function (Blueprint $table) {
            $table->id('recolecta_pago_id');
            $table->unsignedBigInteger('recolecta_id');
            $table->unsignedBigInteger('tipo_pago_id');
            $table->decimal('monto', 9, 2);
            $table->string('numero_referencia', 40)->nullable();
            $table->string('banco_codigo')->nullable();
            $table->string('telefono_pagador')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->string('estatus')->default('reportado'); // reportado | confirmado | rechazado
            $table->unsignedBigInteger('confirmado_por')->nullable();
            $table->timestamp('confirmado_en')->nullable();
            $table->timestamps();

            $table->foreign('recolecta_id')->references('recolecta_id')->on('recolectas');
            $table->foreign('tipo_pago_id')->references('tipo_pago_id')->on('tipos_pagos');
            $table->foreign('confirmado_por')->references('id')->on('users');

            $table->index('recolecta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recolecta_pagos');
    }
};
