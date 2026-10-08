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
        Schema::create('sacas_encaminamiento', function (Blueprint $table) {
            $table->id('saca_encaminamiento_id');
            $table->unsignedBigInteger('saca_id');
            $table->unsignedBigInteger('oficina_id');                 // oficina que tiene la valija tras este movimiento
            $table->unsignedBigInteger('oficina_externa_id')->nullable(); // oficina de origen del movimiento
            $table->unsignedBigInteger('saca_estatus_id');                 // reusa el catálogo envios_estatus
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamps();

            $table->foreign('saca_id')->references('saca_id')->on('sacas');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('oficina_externa_id')->references('oficina_id')->on('oficinas');
            $table->foreign('saca_estatus_id')->references('saca_estatus_id')->on('sacas_estatus');
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sacas_encaminamiento');
    }
};
