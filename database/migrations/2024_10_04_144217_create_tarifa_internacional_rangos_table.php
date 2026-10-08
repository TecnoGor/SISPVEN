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
        Schema::create('tarifa_internacional_rangos', function (Blueprint $table) {
            $table->id('tarifa_internacional_rango_id'); 
            $table->float('desde');
            $table->float('hasta');
            $table->string('grupo');
            $table->float('monto');
            $table->boolean('activo')->default(true);
            $table->integer('medida_id');
            $table->foreign('medida_id')->references('medida_id')->on('medidas');
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
        Schema::dropIfExists('tarifa_internacional_rangos');
    }
};
