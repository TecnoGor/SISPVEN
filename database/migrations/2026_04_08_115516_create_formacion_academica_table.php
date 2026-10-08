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
        Schema::create('formacion_academica', function (Blueprint $table) {
            $table->id('formacion_academica_id');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('nivel_educativo_id');
            $table->unsignedBigInteger('carrera_id')->nullable();
            $table->unsignedBigInteger('institucion_id')->nullable();
            $table->string('carrera_otro')->nullable();
            $table->string('institucion_otro')->nullable();
            $table->unsignedBigInteger('pais_id')->default(90);
            $table->year('año_graduacion')->nullable();
            $table->string('titulo_obtenido')->nullable();

            $table->foreign('empleado_id')->references('empleado_id')->on('empleados');
            $table->foreign('nivel_educativo_id')->references('nivel_educativo_id')->on('niveles_educativos');
            $table->foreign('carrera_id')->references('carrera_estudio_id')->on('carreras_estudio');
            $table->foreign('institucion_id')->references('institucion_id')->on('instituciones');
            $table->foreign('pais_id')->references('pais_id')->on('paises');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formacion_academica');
    }
};
