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
        Schema::create('gastos_operativos', function (Blueprint $table) {
            $table->id('gastos_operativos_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('tipo_gasto_operativo_id');
            $table->float('monto', 9, 2);
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('tipo_gasto_operativo_id')->references('tipo_gasto_operativo_id')->on('tipos_gastos_operativos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gastos_operativos', function (Blueprint $table){
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['tipo_gasto_operativo_id']);
        });
        

        Schema::dropIfExists('gastos_operativos');
    }
};
