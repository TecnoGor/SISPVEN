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
        Schema::create('contratos_almacenamientos', function (Blueprint $table) {
            $table->id('contrato_almacenamiento_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('cliente_corporativo_id');
            $table->float('espacio');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->float('tarifa');
            $table->boolean('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos_alamacenamiento', function (Blueprint $table) {
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['cliente_corporativo_id']);
        });

        Schema::dropIfExists('contratos_almacenamientos');
    }
};
