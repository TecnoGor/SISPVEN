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
        Schema::create('pedidos_imprentas', function (Blueprint $table) {
            $table->id('pedido_imprenta_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('tipo_documento');
            $table->string('documento');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('telefono');
            $table->string('correo');
            $table->float('alto');
            $table->float('ancho');
            $table->date('fecha_entrega');
            $table->string('imagen_path');
            $table->string('nombre_original');
            $table->boolean('activo')->default(true);
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
        Schema::dropIfExists('pedidos_imprentas');
    }
};
