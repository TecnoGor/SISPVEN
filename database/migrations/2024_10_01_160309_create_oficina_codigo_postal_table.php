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
        Schema::create('oficina_codigo_postal', function (Blueprint $table) {
            $table->id('oficina_codigo_postal_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('codigo_postal_id');

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('codigo_postal_id')->references('codigo_postal_id')->on('codigos_postales');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oficina_codigo_postal', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['codigo_postal_id']);
        });
        Schema::dropIfExists('oficina_codigo_postal');
    }
};
