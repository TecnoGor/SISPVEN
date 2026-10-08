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
        Schema::create('envios_encaminamiento', function (Blueprint $table) {
            $table->id('envios_encaminamiento_id');
            $table->integer('envio_id')->nullable();
            $table->integer('oficina_id');
            $table->integer('oficina_externa_id')->nullable();
            $table->integer('usuario_id');
            $table->integer('viaje_id')->nullable();
            $table->integer('estatus_id');
            $table->boolean('devolucion');

            $table->timestamps();

            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('oficina_externa_id')->references('oficina_id')->on('oficinas')->nullable();;
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('viaje_id')->references('viaje_id')->on('viajes')->nullable();;
            $table->foreign('estatus_id')->references('envios_estatus_id')->on('envios_estatus');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios_encaminamiento', function (Blueprint $table) {
            // Eliminar claves foráneas antes de eliminar la tabla
            $table->dropForeign(['envio_id']);
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['oficina_externa_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['viaje_id']);
            $table->dropForeign(['estatus_id']);
        });

        Schema::dropIfExists('envios_encaminamiento');
    }
};
