<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicados', function (Blueprint $table) {
            $table->id('comunicado_id');
            $table->string('codigo')->unique();
            $table->string('tipo');
            $table->string('asunto');
            $table->string('prioridad');

            $table->unsignedBigInteger('remitente_id');
            $table->foreign('remitente_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('respuesta_comunicado_id')->nullable();
            $table->foreign('respuesta_comunicado_id')->references('comunicado_id')->on('comunicados')->onDelete('set null');

            $table->json('datos_json')->nullable();
            $table->timestamp('fecha_limite')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicados');
    }
};
