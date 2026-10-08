<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sacas', function (Blueprint $table) {
            $table->foreignId('numero_despacho_id')
                  ->nullable()
                  ->after('numero_precinto')
                  ->constrained('numeros_despacho_oficina', 'numero_despacho_id');
        });
    }

    public function down(): void
    {
        Schema::table('sacas', function (Blueprint $table) {
            $table->dropForeign(['numero_despacho_id']);
            $table->dropColumn('numero_despacho_id');
        });
    }
};