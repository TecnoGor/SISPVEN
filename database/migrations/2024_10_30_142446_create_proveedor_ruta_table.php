<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedor_ruta', function (Blueprint $table) {
            $table->foreignId('proveedor_id')->constrained('proveedores', 'proveedor_id')->onDelete('cascade');
            $table->foreignId('ruta_id')->constrained('rutas', 'ruta_id')->onDelete('cascade');
            $table->timestamps();

            // Definir la clave primaria compuesta
            $table->primary(['proveedor_id', 'ruta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedor_ruta');
    }
};
