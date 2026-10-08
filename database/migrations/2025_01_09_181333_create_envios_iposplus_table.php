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
        Schema::create('envios_iposplus', function (Blueprint $table) {
            $table->id('envio_iposplus_id');
            $table->unsignedBigInteger('envio_id');
            $table->float('alto');
            $table->float('largo');
            $table->float('ancho');
            $table->timestamps();

            $table->foreign('envio_id')->references('envio_id')->on('envios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios_iposplus', function (Blueprint $table) {
            // Eliminar claves foráneas antes de eliminar la tabla
            $table->dropForeign(['envio_id']);
        });

        Schema::dropIfExists('envios_iposplus');
    }
};
