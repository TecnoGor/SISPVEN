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
        Schema::create('tarifas_internacional_conceptos', function (Blueprint $table) {
            $table->id('tarifa_conceptos_internacional_id');
            $table->string('nombre');
            $table->float('monto');
            $table->boolean('activo')->default(true);
            $table->integer('servicios_id'); 
            $table->foreign('servicios_id')->references('servicio_id')->on('servicios');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas_internacional_conceptos');
    }
};
