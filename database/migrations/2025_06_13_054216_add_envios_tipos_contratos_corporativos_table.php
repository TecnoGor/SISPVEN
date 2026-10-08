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
        Schema::table('contratos_corporativos', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_contrato_id')->nullable();
            $table->integer('cant_envios')->nullable();
            $table->integer('cant_envios_utilizados')->default(0);

            $table->foreign('tipo_contrato_id')->references('tipo_contrato_id')->on('tipos_contratos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos_corporativos', function (Blueprint $table) {
             $table->dropColumn(['tipo_contrato_id']);
        });
    }
};
