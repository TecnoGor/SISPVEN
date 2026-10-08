<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Catálogo de estados del flujo de recolectas (app de clientes).
     * Catálogo propio: no se agregan estados a envios_estatus para no
     * contaminar el tracking postal existente.
     */
    public function up(): void
    {
        Schema::create('recolecta_estatus', function (Blueprint $table) {
            $table->id('recolecta_estatus_id');
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->integer('orden');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recolecta_estatus');
    }
};
