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
        Schema::create('oficinas', function (Blueprint $table) {
            $table->id('oficina_id');
            $table->string('codigo')->nullable();
            $table->unsignedBigInteger('oficina_relacionada_id')->nullable();
            $table->string('nombre');
            $table->unsignedBigInteger('tipo_oficina_id');
            $table->string('jefe_oficina')->nullable();
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('codigo_ubicacion');
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('municipio_id');
            $table->unsignedBigInteger('parroquia_id');
            $table->boolean('zona_economica_especial');
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->unsignedBigInteger('estatus_id');
            $table->boolean('operaciones')->deafult('false');
            $table->timestamps();

            $table->foreign('oficina_relacionada_id')->references('oficina_id')->on('oficinas');
            $table->foreign('tipo_oficina_id')->references('tipo_oficina_id')->on('tipos_oficinas');
            $table->foreign('codigo_ubicacion')->references('codigo_postal')->on('codigos_postales');
            $table->foreign('estado_id')->references('estado_id')->on('estados');
            $table->foreign('municipio_id')->references('municipio_id')->on('municipios');
            $table->foreign('parroquia_id')->references('parroquia_id')->on('parroquias');
            $table->foreign('estatus_id')->references('estatus_oficina_id')->on('estatus_oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oficinas', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['oficina_relacionada_id']);
            $table->dropForeign(['tipo_oficina_id']);
            $table->dropForeign(['estado_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['parroquia_id']);
            $table->dropForeign(['codigo_ubicacion']);
            $table->dropForeign(['estatus']);
        });

        Schema::dropIfExists('oficinas');
    }
};
