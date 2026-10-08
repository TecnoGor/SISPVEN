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
        Schema::create('manifiestos', function (Blueprint $table) {
            $table->id('manifiesto_id'); // Llave primaria
            $table->integer('oficina_id');
            $table->integer('oficina_destino_id');
            $table->boolean('status')->default(false); // Estado con valor por defecto
            $table->timestamps(); // Created_at y updated_at



            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('oficina_destino_id')->references('oficina_id')->on('oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifiestos'); // Elimina la tabla
    }
};
