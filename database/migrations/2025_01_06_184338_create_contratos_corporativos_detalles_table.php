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
        Schema::create('contratos_corporativos_detalles', function (Blueprint $table) {
            $table->id('contrato_corporativo_detalle_id');
            $table->unsignedBigInteger('cliente_corporativo_id');
            $table->unsignedBigInteger('contrato_corporativo_id')->nullable();
            $table->unsignedBigInteger('contrato_almacenamiento_id')->nullable();
            $table->float('cuota');
            $table->date('fecha_limite');
            $table->boolean('cancelada');
            $table->date('fecha_cancelada')->nullable();
            $table->timestamps();

            $table->foreign('cliente_corporativo_id')->references('cliente_corporativo_id')->on('clientes_corporativos');
            $table->foreign('contrato_corporativo_id')->references('contrato_corporativo_id')->on('contratos_corporativos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos_corporativos_detalles');
    }
};
