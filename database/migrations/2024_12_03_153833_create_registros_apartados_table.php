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
        Schema::create('registros_apartados', function (Blueprint $table) {
            $table->id('registro_apartado_id');
            $table->unsignedBigInteger('servicio_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('codigo_apartado_id')->nullable();
            $table->string('tipo_documento');
            $table->string('documento');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('codigo_postal');
            $table->string('telefono');
            $table->string('correo');
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('codigo_apartado_id')->references('codigo_apartado_id')->on('codigos_apartados');
            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
            $table->foreign('codigo_postal')->references('codigo_postal')->on('codigos_postales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registros_apartados', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['oficinas']);
            $table->dropForeign(['codigo_apartado_id']);
            $table->dropForeign(['servicio_id']);
            $table->dropForeign(['codigo_postal']);
        });

        Schema::dropIfExists('registros_apartados');
    }
};
