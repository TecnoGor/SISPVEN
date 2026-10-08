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
        Schema::create('tarifas_exporta_facil', function (Blueprint $table) {
            $table->id('tarifa_exporta_facil_id');
            $table->unsignedBigInteger('pais_id');
            $table->float('monto');
            $table->boolean('activo')->default(true);
            $table->integer('medida_id');
            $table->timestamps();

            $table->foreign('pais_id')->references('pais_id')->on('paises');
            $table->foreign('medida_id')->references('medida_id')->on('medidas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas_exporta_facil');
    }
};
