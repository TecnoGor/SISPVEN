<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicado_adjuntos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('comunicado_id');
            $table->foreign('comunicado_id')->references('comunicado_id')->on('comunicados')->onDelete('cascade');

            $table->string('nombre_original');
            $table->string('ruta_archivo');
            $table->string('tipo_mime')->nullable();
            $table->unsignedBigInteger('tamano')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicado_adjuntos');
    }
};
