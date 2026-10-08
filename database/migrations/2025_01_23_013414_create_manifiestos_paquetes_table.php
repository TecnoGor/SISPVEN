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
        Schema::create('manifiestos_paquetes', function (Blueprint $table) {
            $table->id('manifiestos_paquetes_id');
            $table->integer('manifiesto_id');
            $table->foreign('manifiesto_id')->references('manifiesto_id')->on('manifiestos');
            $table->integer('saca_id')->nullable();
            $table->foreign('saca_id')->references('saca_id')->on('sacas');
            $table->integer('envio_id')->nullable();
            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->integer('servicio_id')->nullable();
            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
            $table->integer('peso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifiestos_paquetes');
    }
};
