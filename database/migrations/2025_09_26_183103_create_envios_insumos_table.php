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
        Schema::create('envios_insumos', function (Blueprint $table) {
            $table->id('envio_insumo_id');
            $table->unsignedBigInteger('envio_id');
            $table->unsignedBigInteger('insumo_id');
            $table->float('coste', 9, 2);
            $table->timestamps();

            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('insumo_id')->references('insumo_id')->on('insumos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios_insumos');
    }
};
