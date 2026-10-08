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
        Schema::create('pagos_servicios_publicos', function (Blueprint $table) {
            $table->id('pago_servicio_publico_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('servicio_publico_id');
            $table->date('mensualidad');
            $table->float('monto');
            $table->boolean('estatus');
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('servicio_publico_id')->references('servicio_publico_id')->on('servicios_publicos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_servicios_publicos');
    }
};
