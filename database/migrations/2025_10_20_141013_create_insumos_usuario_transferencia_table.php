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
        Schema::create('insumos_usuario_transferencia', function (Blueprint $table) {
            $table->id('insumo_usuario_transferencia_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('insumo_id');
            $table->integer('cantidad');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('insumo_id')->references('insumo_id')->on('insumos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos_usuario_transferencia');
    }
};
