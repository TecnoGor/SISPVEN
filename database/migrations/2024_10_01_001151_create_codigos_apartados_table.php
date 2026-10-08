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
        Schema::create('codigos_apartados', function (Blueprint $table) {
            $table->id('codigo_apartado_id');
            $table->string('apartado');
            $table->unsignedBigInteger('oficina_id');
            $table->boolean('operativo');
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigos_apartados');
    }
};
