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
        Schema::create('mantenimiento', function (Blueprint $table) {
            $table->id('mantenimiento_id'); 
            $table->unsignedBigInteger('vehiculo_id');
            $table->foreign('vehiculo_id')->references('vehiculo_id')->on('vehiculos')->onDelete('cascade');
            $table->string('descripcion'); 
            $table->date('fecha'); 
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento');
    }
};
