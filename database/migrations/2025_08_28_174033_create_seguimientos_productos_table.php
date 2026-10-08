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
        Schema::create('seguimientos_productos', function (Blueprint $table) {
            $table->id('seguimiento_producto_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('producto_id');
            $table->boolean('operacion')->default(true);
            $table->integer('cantidad_registrada');
            $table->timestamps();

            $table->foreign('producto_id')->references('producto_id')->on('productos');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
