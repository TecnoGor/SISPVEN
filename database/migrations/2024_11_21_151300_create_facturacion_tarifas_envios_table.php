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
        Schema::create('facturacion_tarifas_envios', function (Blueprint $table) {
            $table->id('facturacion_tarifas_envios_id');
            $table->unsignedBigInteger('envio_id');
            $table->unsignedBigInteger('envio_exporta_facil_id')->nullable();
            $table->unsignedBigInteger('tarifa_id');
            $table->string('tipo_envio');
            $table->timestamps();
            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('envio_exporta_facil_id')->references('envio_exporta_facil_id')->on('envios_exporta_facil');
            $table->foreign('tarifa_id')->references('tarifa_conceptos_id')->on('tarifas_nacionales_conceptos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturacion_tarifas_envios', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['envio_id']);
            $table->dropForeign(['envio_exporta_facil_id']);
            $table->dropForeign(['tarifa_id']);
        });

        Schema::dropIfExists('facturacion_tarifas_envios');
    }
};
