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
        Schema::create('contratos_corporativos', function (Blueprint $table) {
            $table->id('contrato_corporativo_id');
            $table->unsignedBigInteger('cliente_corporativo_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->integer('peso_contrato')->nullable();
            $table->integer('peso_utilizado')->default(0);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->float('tarifa')->nullable();
            $table->boolean('activo');

            $table->timestamps();

            $table->foreign('cliente_corporativo_id')->references('cliente_corporativo_id')->on('clientes_corporativos');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos_corporativos', function (Blueprint $table) {
            $table->dropForeign(['cliente_corporativo_id']);
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['usuario_id']);
        });

        Schema::dropIfExists('contratos_corporativos');
    }
};
