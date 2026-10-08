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
        Schema::create('oficinas_vehiculos', function (Blueprint $table) {
            $table->id('oficina_vehiculo_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('vehiculo_id');
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('vehiculo_id')->references('vehiculo_id')->on('vehiculos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficinas_vehiculos');
    }
};
