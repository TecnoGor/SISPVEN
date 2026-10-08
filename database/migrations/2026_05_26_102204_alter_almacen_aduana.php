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
        Schema::table('almacen_aduana', function (Blueprint $table) {
    // FKs faltantes
    $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
    $table->foreign('envio_id')->references('envio_id')->on('envios');

    $table->index(['oficina_id', 'estatus']);
    $table->index('envio_id');

    $table->unsignedBigInteger('usuario_ingreso_id')->nullable();
    $table->unsignedBigInteger('usuario_salida_id')->nullable();
    $table->foreign('usuario_ingreso_id')->references('id')->on('users');
    $table->foreign('usuario_salida_id')->references('id')->on('users');
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
