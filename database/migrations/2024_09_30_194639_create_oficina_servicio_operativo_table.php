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
        Schema::create('oficina_servicio_operativo', function (Blueprint $table) {
            $table->id('oficina_servicio_operativo_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('servicio_operativo_id');

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('servicio_operativo_id')->references('servicio_operativo_id')->on('servicios_operativos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oficina_servicio_operativo', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['servicio_operativo_id']);
        });

        Schema::dropIfExists('oficina_servicio_operativo');
    }
};
