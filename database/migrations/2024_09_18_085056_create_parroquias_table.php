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
        Schema::create('parroquias', function (Blueprint $table) {
            $table->id('parroquia_id');
            $table->unsignedBigInteger('municipio_id');
            $table->string('nombre');
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('municipio_id')->references('municipio_id')->on('municipios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parroquias', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['municipio_id']);
        });

        Schema::dropIfExists('parroquias');
    }
};
