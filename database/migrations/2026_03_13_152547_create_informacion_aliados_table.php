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
        Schema::create('informacion_aliados', function (Blueprint $table) {
            $table->id('informacion_aliado_id');
            $table->unsignedBigInteger('oficina_id');
            $table->string('RIF')->nullable();
            $table->string('nro_contrato')->nullable();
            $table->date('fecha_contratacion')->nullable();
            $table->float('tarifa_aplicada', 9, 2)->nullable();
            $table->string('tipo_facturacion')->nullable();
            $table->string('tiempo_entrega_zona')->nullable();
            $table->string('tiempo_entrega_estado')->nullable();
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('informacion_aliados', function (Blueprint $table) {
            $table->dropForeign(['oficina_id']);
        });
        Schema::dropIfExists('informacion_aliados');
    }
};
