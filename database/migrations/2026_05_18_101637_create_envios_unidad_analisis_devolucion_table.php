<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envios_unidad_analisis_devolucion', function (Blueprint $table) {
            $table->id('envio_unidad_analisis_devolucion_id');

            $table->unsignedBigInteger('envio_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_ingreso_id');
            $table->unsignedBigInteger('usuario_decision_id')->nullable();

            // true = está físicamente en la unidad; false = ya salió
            $table->boolean('estatus')->default(true);

            $table->string('observaciones_decision')->nullable();

            // A dónde decidió la unidad enviar el envío (almacén, rezago, etc.)
            $table->unsignedBigInteger('decision_final')->nullable();

            $table->timestamp('fecha_ingreso')->useCurrent();
            $table->timestamp('fecha_decision')->nullable();
            $table->timestamps();

            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('usuario_ingreso_id')->references('id')->on('users');
            $table->foreign('usuario_decision_id')->references('id')->on('users');
            $table->foreign('decision_final')->references('envios_estatus_id')->on('envios_estatus');

            $table->index('envio_id');
            $table->index(['oficina_id', 'estatus']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envios_unidad_analisis_devolucion');
    }
};
