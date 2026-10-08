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
        Schema::create('paises', function (Blueprint $table) {
            $table->id('pais_id');
            $table->unsignedBigInteger('continente_id');
            $table->string('nombre');
            $table->boolean('activo')->nullable();
            $table->timestamps();
            $table->foreign('continente_id')->references('continente_id')->on('continentes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paises', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['continente_id']);
        });

        Schema::dropIfExists('paises');
    }
};
