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
        Schema::create('motivo_npc_oficina', function (Blueprint $table) {
            $table->id('motivo_npc_oficina_id');
            $table->unsignedBigInteger('oficina_id');
            $table->string('motivo');
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motivo_npc_oficina');
    }
};
