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
        Schema::create('vehiculos_propios', function (Blueprint $table) {
            $table->id('vehiculo_propio_id');
            
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('placa')->unique();
            $table->string('color');
            $table->string('marca');
            $table->string('modelo');
            $table->year('año');
            $table->string('numero_poliza');
            $table->date('fecha_vencimiento');
            $table->integer('capacidad_carga');
            
            $table->unsignedBigInteger('oficina_id');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas')->onDelete('set null');
            
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos_propios');
    }
};
