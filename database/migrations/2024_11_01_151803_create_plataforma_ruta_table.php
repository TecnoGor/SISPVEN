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
        Schema::create('plataforma_ruta', function (Blueprint $table) {
            $table->id('plataforma_ruta_id');
            $table->integer('ruta_id');
            $table->foreign('ruta_id')->references('ruta_id')->on('rutas');
            $table->integer('estado_id');
            $table->foreign('estado_id')->references('estado_id')->on('estados');
            $table->string('siglas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plataforma_ruta');
    }
};
