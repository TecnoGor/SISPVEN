<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario_seguimiento', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('descripcion');
            $table->string('user_agent', 500)->nullable()->after('ip_address');

            // Permitir usuario_id nulo para registrar intentos de login fallidos
            // cuando el email no corresponde a ningún usuario existente.
            $table->unsignedBigInteger('usuario_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('usuario_seguimiento', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'user_agent']);
            $table->unsignedBigInteger('usuario_id')->nullable(false)->change();
        });
    }
};
