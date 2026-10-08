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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id('proveedor_id'); 
            $table->string('representante_legal'); 
            $table->string('telefono'); 
            $table->string('cedula'); 
            $table->string('correo')->unique(); 
            $table->string('rif'); 
            $table->string('razon_social'); 
            $table->string('direccion_fiscal'); 
            $table->string('retencion')->nullable(); 
            $table->string('contribuyente_especial')->nullable(); 
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
        Schema::dropIfExists('proveedores');
    }
};
