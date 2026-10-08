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
        Schema::create('envios_incidencias', function (Blueprint $table) {
            $table->id('envio_incidencia_id');
            $table->unsignedBigInteger('envio_id');
            $table->string('detalle', 350);
            $table->text('imagen_incidencia')->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('oficina_id');
            $table->timestamps();

            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios_incidencias', function (Blueprint $table) {
            $table->dropForeign(['envio_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['oficina_id']);
        });

        Schema::dropIfExists('envios_incidencias');
    }
};
