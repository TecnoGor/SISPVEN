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
        Schema::create('sectores', function (Blueprint $table) {
            $table->id('sector_id');
            $table->string('codigo_postal');
            $table->string('nombre');
            $table->unsignedBigInteger('parroquia_id');
            $table->boolean('activo')->nullable();
            $table->timestamps();

            $table->foreign('parroquia_id')->references('parroquia_id')->on('parroquias');
            $table->foreign('codigo_postal')->references('codigo_postal')->on('codigos_postales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sectores', function (Blueprint $table) {
            // Eliminar claves foráneas antes de eliminar la tabla
            $table->dropForeign(['codigo']);
            $table->dropForeign(['parroquia_id']);
        });

        Schema::dropIfExists('sectores');
    }
};
