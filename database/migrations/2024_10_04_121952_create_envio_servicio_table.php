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
        Schema::create('envio_servicio', function (Blueprint $table) {
            $table->id('envio_servicio_id');
            $table->unsignedBigInteger('envio_id');
            $table->unsignedBigInteger('servicio_id');

            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envio_servicio', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['envio_id']);
            $table->dropForeign(['servicio_id']);
        });

        Schema::dropIfExists('envio_servicio');
    }
};
