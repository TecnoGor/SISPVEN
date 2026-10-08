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
        Schema::create('tipos_sacas', function (Blueprint $table) {
            $table->id('tipo_saca_id');
            $table->string('nombre');
            $table->unsignedBigInteger('servicio_id');
            $table->boolean('certificado');
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_sacas');
    }
};
