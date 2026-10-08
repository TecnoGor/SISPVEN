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
            $table->foreign('contrato_almacenamiento_id')
                ->references('contrato_almacenamiento_id')
                ->on('contratos_almacenamientos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            //
        });
    }
};
