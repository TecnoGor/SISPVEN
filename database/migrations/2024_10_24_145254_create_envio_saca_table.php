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
        Schema::create('envio_saca', function (Blueprint $table) {
            $table->id('envio_saca_id');
            $table->unsignedBigInteger('envio_id');
            $table->unsignedBigInteger('saca_id');
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('saca_id')->references('saca_id')->on('sacas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envio_saca');
    }
};
