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
        Schema::table('envios', function (Blueprint $table) {
            $table->unsignedBigInteger('contrato_corporativo_id')->nullable();
            $table->unsignedBigInteger('cliente_corporativo_autorizado_id')->nullable();

            $table->foreign('contrato_corporativo_id')->references('contrato_corporativo_id')->on('contratos_corporativos');
            $table->foreign('cliente_corporativo_autorizado_id')->references('cliente_corporativo_autorizado_id')->on('cliente_corporativo_autorizados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
