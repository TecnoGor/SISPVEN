<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo oficial de motivos de devolución usado por la pestaña 7.3
 * de la app móvil Cartero IPOSTEL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motivos_devolucion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40)->unique();
            $table->string('nombre', 120);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motivos_devolucion');
    }
};
