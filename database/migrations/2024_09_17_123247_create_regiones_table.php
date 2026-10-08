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
        Schema::create('regiones', function (Blueprint $table) {
            $table->id('region_id');
            $table->unsignedBigInteger('pais_id')->nullable();
            $table->string('nombre');
            $table->boolean('activo')->nullable();
            $table->timestamps();

            $table->foreign('pais_id')->references('pais_id')->on('paises');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regiones', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['pais_id']);
        });

        Schema::dropIfExists('regiones');
    }
};
