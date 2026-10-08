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
        Schema::create('compras_filatelia', function (Blueprint $table) {
            $table->id('compra_filatelia_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->float('coste', 9, 2);
            $table->string('tipo_documento');
            $table->string('documento');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('telefono');
            $table->string('correo');
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras_filatelia');
    }
};
