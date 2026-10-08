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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('cliente_id');
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();;
            $table->string('tipo_documento')->nullable();;
            $table->bigInteger('numero_documento')->nullable();;
            $table->string('telefono')->nullable()->nullable();;
            $table->string('correo')->nullable()->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
