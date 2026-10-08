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
        Schema::create('envios', function (Blueprint $table) {
            $table->id('envio_id');
            $table->unsignedBigInteger('servicio_id')->nullable();
            $table->string('tipo_envio')->nullable();
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('nombre_rem')->nullable();
            $table->string('apellido_rem')->nullable();
            $table->string('tipo_documento_rem')->nullable();
            $table->string('documento_rem')->nullable();
            $table->integer('codigo_postal_rem')->nullable();
            $table->string('estado_rem')->nullable();
            $table->string('municipio_rem')->nullable();
            $table->string('parroquia_rem')->nullable();
            $table->string('ciudad_rem')->nullable();
            $table->string('direccion_rem')->nullable();
            $table->string('correo_rem')->nullable();
            $table->string('telefono_rem')->nullable();
            $table->string('nombre_dest')->nullable();
            $table->string('apellido_dest')->nullable();
            $table->string('tipo_documento_dest')->nullable();
            $table->string('documento_dest')->nullable();
            $table->integer('codigo_postal_dest')->nullable();
            $table->unsignedBigInteger('continente_dest')->nullable();
            $table->unsignedBigInteger('pais_dest')->nullable();
            $table->unsignedBigInteger('estado_dest')->nullable();
            $table->unsignedBigInteger('oficina_dest_id')->nullable();
            $table->unsignedBigInteger('municipio_dest')->nullable();
            $table->unsignedBigInteger('parroquia_dest')->nullable();
            $table->unsignedBigInteger('ciudad_dest')->nullable();
            $table->string('direccion_dest')->nullable();
            $table->string('tlf_dest')->nullable();
            $table->string('correo_dest')->nullable();
            $table->string('servicio_expreso')->nullable();
            $table->string('peso')->nullable();
            $table->float('coste')->nullable();
            $table->string('contenido')->nullable();
            $table->unsignedBigInteger('apartado_postal')->nullable();
            $table->string('codigo_envio')->nullable();
            $table->unsignedBigInteger('tipo_saca_id')->nullable();
            $table->boolean('devolucion');
            $table->boolean('descubierto');
            $table->timestamps();

            $table->foreign('continente_dest')->references('continente_id')->on('continentes');
            $table->foreign('pais_dest')->references('pais_id')->on('paises');
            $table->foreign('estado_dest')->references('estado_id')->on('estados');
            $table->foreign('municipio_dest')->references('municipio_id')->on('municipios');
            $table->foreign('parroquia_dest')->references('parroquia_id')->on('parroquias');
            $table->foreign('ciudad_dest')->references('ciudad_id')->on('ciudades');
            $table->foreign('tipo_saca_id')->references('tipo_saca_id')->on('tipos_sacas');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('oficina_dest_id')->references('oficina_id')->on('oficinas');
            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('apartado_postal')->references('codigo_apartado_id')->on('codigos_apartados');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            // Eliminar claves foráneas antes de eliminar la tabla
            $table->dropForeign(['tipo_saca_id']);
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['oficina_dest_id']);
            $table->dropForeign(['servicio_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['apartado_postal']);
        });

        Schema::dropIfExists('envios');
    }
};
