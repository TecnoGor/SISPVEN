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
        Schema::table('comunicado_destinatario', function (Blueprint $table) {
            $table->unsignedBigInteger('motivo_id')->nullable()->after('estatus_id');
            // Assuming the foreign table is named 'estatus_comunicaciones' based on the EstatusComunicacion model
            $table->foreign('motivo_id', 'fk_motivo_estatus')->references('id')->on('estatus_comunicaciones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comunicado_destinatario', function (Blueprint $table) {
            $table->dropForeign('fk_motivo_estatus');
            $table->dropColumn('motivo_id');
        });
    }
};
