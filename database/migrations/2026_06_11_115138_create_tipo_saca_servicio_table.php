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
        Schema::create('tipo_saca_servicio', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_saca_id');
            $table->unsignedBigInteger('servicio_id');

            $table->primary(['tipo_saca_id', 'servicio_id']);

            $table->foreign('tipo_saca_id')->references('tipo_saca_id')->on('tipos_sacas');

            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_saca_servicio');
    }
};
