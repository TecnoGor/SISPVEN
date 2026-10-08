<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunicado_destinatario', function (Blueprint $table) {
            if (!Schema::hasColumn('comunicado_destinatario', 'emisor_id')) {
                $table->foreignId('emisor_id')
                      ->nullable()
                      ->after('usuario_id')
                      ->constrained('users')
                      ->nullOnDelete();
            }

            if (!Schema::hasColumn('comunicado_destinatario', 'observacion')) {
                $table->text('observacion')->nullable()->after('estatus_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comunicado_destinatario', function (Blueprint $table) {
            if (Schema::hasColumn('comunicado_destinatario', 'observacion')) {
                $table->dropColumn('observacion');
            }
            if (Schema::hasColumn('comunicado_destinatario', 'emisor_id')) {
                $table->dropForeign(['emisor_id']);
                $table->dropColumn('emisor_id');
            }
        });
    }
};
