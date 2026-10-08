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
        Schema::create('insumos_ventas', function (Blueprint $table) {
            $table->id('insumo_venta_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('envio_id');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('envio_id')->references('envio_id')->on('envios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos_ventas');
    }
};
