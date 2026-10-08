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
        Schema::create('envios_distribucion_paquetes_muestras', function (Blueprint $table) {
            $table->id('envio_distribucion_paquete_muestra_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('envio_id');

            // true = está físicamente en distribución; false = ya salió
            $table->boolean('estatus')->default(true);

            $table->date('Entrada');
            $table->date('Salida')->nullable();

            $table->unsignedBigInteger('usuario_ingreso_id');
            $table->unsignedBigInteger('usuario_salida_id')->nullable();

            $table->timestamps();

            $table->index(['oficina_id', 'estatus']);
            $table->index('envio_id');

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('usuario_ingreso_id')->references('id')->on('users');
            $table->foreign('usuario_salida_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios_distribucion_paquetes_muestras');
    }
};
