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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id('direccion_id');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('municipio_id');
            $table->unsignedBigInteger('parroquia_id');
            $table->unsignedBigInteger('sector_id');
            $table->string('direccion_especifica');
            $table->boolean('principal')->default(false);
            $table->timestamps();

            $table->foreign('empleado_id')->references('empleado_id')->on('empleados');
            $table->foreign('estado_id')->references('estado_id')->on('estados');
            $table->foreign('municipio_id')->references('municipio_id')->on('municipios');
            $table->foreign('parroquia_id')->references('parroquia_id')->on('parroquias');
            $table->foreign('sector_id')->references('sector_id')->on('sectores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
