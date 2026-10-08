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
        Schema::table('facturaciones_contratos', function (Blueprint $table) {
            // Nueva columna para almacenamiento
            $table->unsignedBigInteger('contrato_almacenamiento_id')->nullable();

            // Hacer corporativo nullable para que coexistan
            $table->unsignedBigInteger('contrato_corporativo_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturaciones_contratos', function (Blueprint $table) {
            $table->dropColumn('contrato_almacenamiento_id');
            $table->unsignedBigInteger('contrato_corporativo_id')->nullable(false)->change();
        });
    }
};
