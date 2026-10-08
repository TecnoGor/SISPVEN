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
        Schema::create('municipios', function (Blueprint $table) {
            $table->id('municipio_id');
            $table->unsignedBigInteger('estado_id');
            $table->string('nombre');
            $table->string('codigo')->nullable();
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('estado_id')->references('estado_id')->on('estados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('municipios', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['estado_id']);
        });


        Schema::dropIfExists('municipios');
    }
};
