<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos_sacas', function (Blueprint $table) {
            // Valijas que se despachan por peso: no admiten envios dentro,
            // se cierran vacias y exigen peso al cerrar.
            $table->boolean('cargar_por_peso')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('tipos_sacas', function (Blueprint $table) {
            $table->dropColumn('cargar_por_peso');
        });
    }
};
