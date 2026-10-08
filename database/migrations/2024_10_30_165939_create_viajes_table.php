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
        Schema::create('viajes', function (Blueprint $table) {
            $table->id('viaje_id');
            $table->integer('ruta_id');
            $table->foreign('ruta_id')->references('ruta_id')->on('rutas');
            $table->string('codigo');
            $table->integer('dia_semana_id');
            $table->foreign('dia_semana_id')->references('dia_semana_id')->on('dias_semana');
            $table->date('fecha_salida');
            $table->integer('vehiculo_id');
            $table->foreign('vehiculo_id')->references('vehiculo_id')->on('vehiculos');
            $table->integer('proveedor_id')->nullable(); // Este campo puede ser nulo
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedores');
            $table->boolean('propio')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
