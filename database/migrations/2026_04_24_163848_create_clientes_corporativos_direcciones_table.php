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
        Schema::create('clientes_corporativos_direcciones', function (Blueprint $table) {
            $table->id('cliente_corporativo_direccion_id');
            $table->unsignedBigInteger('cliente_corporativo_id');
            $table->string('alias');
            $table->string('persona')->nullable();
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('municipio_id');
            $table->unsignedBigInteger('ciudad_id');
            $table->unsignedBigInteger('parroquia_id');
            $table->string('codigo_postal');
            $table->string('direccion');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('cliente_corporativo_id')->references('cliente_corporativo_id')->on('clientes_corporativos');
            $table->foreign('estado_id')->references('estado_id')->on('estados');
            $table->foreign('municipio_id')->references('municipio_id')->on('municipios');
            $table->foreign('parroquia_id')->references('parroquia_id')->on('parroquias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_corporativos_direcciones');
    }
};
