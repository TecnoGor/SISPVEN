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
            $table->float('tasa_aplicada')->nullable();
            $table->float('monto_divisa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturaciones_contratos', function (Blueprint $table) {
            $table->dropColumn(['tasa_aplicada', 'monto_divisa']);
        });
    }
};
