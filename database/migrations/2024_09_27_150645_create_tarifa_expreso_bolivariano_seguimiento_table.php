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
        Schema::create('tarifa_expreso_bolivariano_seguimiento', function (Blueprint $table) {
            $table->id(); 
            $table->integer('tarifa_expreso_bolivariano_seguimiento_id'); 
            $table->integer('usuario_seguimiento_id'); 
            $table->integer('tarifa_expreso_bolivariano_id'); 
            $table->foreign('tarifa_expreso_bolivariano_id')->references('tarifa_expreso_bolivariano_id')->on('tarifa_expreso_bolivariano');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifa_expreso_bolivariano_seguimiento');
    }
};
