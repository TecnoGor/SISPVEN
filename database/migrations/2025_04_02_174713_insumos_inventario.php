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
        Schema::create('insumos_inventario', function (Blueprint $table) {
            $table->id('insumo_inventario_id');
            $table->unsignedBigInteger('oficina_id')->nullable();
            $table->unsignedBigInteger('insumo_id');
            $table->integer('cantidad');
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('insumo_id')->references('insumo_id')->on('insumos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insumos_inventario', function (Blueprint $table) {
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['insumo_id']);
        });

        Schema::dropIfExists('insumos_inventario');
    }
};
