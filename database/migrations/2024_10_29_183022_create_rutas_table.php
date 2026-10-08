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
        Schema::create('rutas', function (Blueprint $table) {
            $table->id('ruta_id');
            $table->integer('distancia');
            $table->string('ruta');
            $table->unsignedBigInteger('oficina_id_origen'); // Cambiar a unsignedBigInteger
            $table->foreign('oficina_id_origen')->references('oficina_id')->on('oficinas');
            $table->unsignedBigInteger('oficina_id_destino'); // Cambiar a unsignedBigInteger
            $table->foreign('oficina_id_destino')->references('oficina_id')->on('oficinas');
            $table->string('tiempo');
            $table->boolean('activo')->default(true); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
