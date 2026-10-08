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
        Schema::create('facturaciones_contratos', function (Blueprint $table) {
            $table->id('facturacion_contrato_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('contrato_corporativo_id');
            $table->unsignedBigInteger('contrato_corporativo_detalle_id');
            $table->unsignedInteger('tipo_pago_id');
            $table->float('monto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturaciones_contratos');
    }
};
