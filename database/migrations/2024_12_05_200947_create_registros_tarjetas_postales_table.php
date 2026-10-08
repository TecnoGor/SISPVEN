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
        Schema::create('registros_tarjetas_postales', function (Blueprint $table) {
            $table->id('registro_tarjeta_postal_id');
            $table->unsignedBigInteger('servicio_id');
            $table->unsignedBigInteger('usuario_id');
            $table->integer('cantidad_tarjetas');
            $table->unsignedBigInteger('oficina_id');
            $table->string('tipo_documento');
            $table->string('documento');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('telefono');
            $table->string('correo');
            $table->timestamps();

            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registros_tarjetas_postales', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['servicio_id']);
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['usuario_id']);
        });

        Schema::dropIfExists('registros_tarjetas_postales');
    }
};
