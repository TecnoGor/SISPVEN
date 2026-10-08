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
        Schema::create('mantenimiento_detalle', function (Blueprint $table) {
            $table->id('mantenimiento_detalle_id');
            $table->unsignedBigInteger('mantenimiento_id');
            $table->foreign('mantenimiento_id')->references('mantenimiento_id')->on('mantenimiento')->onDelete('cascade');
            $table->unsignedBigInteger('servicios_flota_id');
            $table->foreign('servicios_flota_id')->references('servicios_flota_id')->on('servicios_flota')->onDelete('cascade');
            $table->date('fecha'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_detalle');
    }
};
