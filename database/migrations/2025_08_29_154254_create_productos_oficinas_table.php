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
        Schema::create('productos_oficinas', function (Blueprint $table) {
            $table->id('producto_oficina_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad')->default(0);


            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('producto_id')->references('producto_id')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_oficinas');
    }
};
