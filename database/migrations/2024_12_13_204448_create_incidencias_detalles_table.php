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
        Schema::create('incidencias_detalles', function (Blueprint $table) {
            $table->id('incidencia_detalle_id');
            $table->unsignedBigInteger('envio_incidencia_id');
            $table->unsignedBigInteger('incidencia_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias_detalles', function (Blueprint $table) {
            $table->fropForeign(['envio_incidencia_id']);
            $table->fropForeign(['incidencia_id']);
        });

        Schema::dropIfExists('incidencias_detalles');
    }
};
