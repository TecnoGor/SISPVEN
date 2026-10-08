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
        Schema::create('clientes_corporativos', function (Blueprint $table) {
            $table->id('cliente_corporativo_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('tipo_documento');
            $table->integer('numero_documento');
            $table->string('razon_social');
            $table->string('agente_autorizado');
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('municipio_id');
            $table->unsignedBigInteger('parroquia_id');
            $table->integer('codigo_postal');
            $table->string('latitud');
            $table->string('longitud');
            $table->string('direccion');
            $table->string('telefono');
            $table->string('correo');
            $table->boolean('activo');

            $table->foreign('estado_id')->references('estado_id')->on('estados');
            $table->foreign('municipio_id')->references('municipio_id')->on('municipios');
            $table->foreign('parroquia_id')->references('parroquia_id')->on('parroquias');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes_corporativos', function (Blueprint $table) {
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['estado_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['parroquia_id']);
        });

        Schema::dropIfExists('clientes_corporativos');
    }
};
