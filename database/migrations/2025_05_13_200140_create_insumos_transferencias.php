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
        Schema::create('insumos_transferencias', function (Blueprint $table) {
            $table->id('insumo_transferencia_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('insumo_id');
            $table->integer('cantidad');
            $table->unsignedBigInteger('oficina_origen')->nullable();
            $table->unsignedBigInteger('oficina_destino')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('oficina_origen')->references('oficina_id')->on('oficinas');
            $table->foreign('oficina_destino')->references('oficina_id')->on('oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insumos_transferencias', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['oficina_origen']);
            $table->dropForeign(['insumo_destino']);
        });

        Schema::dropIfExists('insumos_transferencias');
    }
};
