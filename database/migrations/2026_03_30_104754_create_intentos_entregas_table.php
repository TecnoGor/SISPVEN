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
        Schema::create('intentos_entregas', function (Blueprint $table) {
            $table->id('intento_entrega_id');
            $table->unsignedBigInteger('envio_almacen_id');
            $table->unsignedBigInteger('envio_id');
            $table->unsignedBigInteger('usuario_id');
            $table->timestamps();

            $table->foreign('envio_almacen_id')->references('envio_almacen_id')->on('envios_almacen');
            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intentos_entregas');
    }
};
