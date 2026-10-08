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
        Schema::create('oficinas_semaforo_postal', function (Blueprint $table) {
            $table->id('oficina_semaforo_postal_id');
            $table->unsignedBigInteger('oficina_id');
            $table->string('condicion');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oficinas_semaforo_postal', function (Blueprint $table) {
            // Eliminar claves foráneas antes de eliminar la tabla
            $table->dropForeign(['oficina_id']);
        });

        Schema::dropIfExists('oficinas_semaforo_postal');
    }
};
