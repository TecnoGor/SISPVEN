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
        Schema::create('cliente_corporativo_autorizados', function (Blueprint $table) {
            $table->id('cliente_corporativo_autorizado_id');
            $table->unsignedBigInteger('cliente_corporativo_id');
            $table->boolean('activo');
            $table->string('nombre');
            $table->string('tipo_documento');
            $table->integer('documento');
            $table->string('cargo');
            $table->string('correo');
            $table->string('telefono');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_corporativo_autorizados');
    }
};
