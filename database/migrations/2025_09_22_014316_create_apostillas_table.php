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
        Schema::create('apostillas', function (Blueprint $table) {
            $table->id('apostilla_id');
            $table->unsignedBigInteger('documento_apostilla_id');
            $table->unsignedBigInteger('cita_apostilla_id');
            $table->timestamps();

            $table->foreign('documento_apostilla_id')->references('documento_apostilla_id')->on('documentos_apostillas');
            $table->foreign('cita_apostilla_id')->references('cita_apostilla_id')->on('citas_apostillas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apostillas');
    }
};
