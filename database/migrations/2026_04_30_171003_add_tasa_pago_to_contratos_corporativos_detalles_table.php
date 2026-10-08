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
        Schema::table('contratos_corporativos_detalles', function (Blueprint $table) {
            $table->float('tasa_pago', 9, 2)->nullable();
            $table->float('monto_bs_pagado', 9, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos_corporativos_detalles', function (Blueprint $table) {
            $table->dropColumn(['tasa_pago', 'monto_bs_pagado']);
        });
    }
};
