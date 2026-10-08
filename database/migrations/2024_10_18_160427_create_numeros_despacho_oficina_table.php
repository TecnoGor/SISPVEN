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
        Schema::create('numeros_despacho_oficina', function (Blueprint $table) {
            $table->id('numero_despacho_id');
            $table->foreignId('oficina_id')->references('oficina_id')->on('oficinas');
            $table->string('numero_despacho');
             $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numeros_despacho_oficina');
    }
};
