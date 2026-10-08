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
        Schema::table('contratos_almacenamientos', function (Blueprint $table) {
            $table->unsignedBigInteger('parametro_id')->nullable();
            $table->float('monto_divisa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos_almacenamientos', function (Blueprint $table) {
            $table->dropColumn(['parametro_id', 'monto_divisa']);
        });
    }
};
