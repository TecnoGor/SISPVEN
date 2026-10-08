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
        Schema::create('telegramas_recibidos', function (Blueprint $table) {
            $table->id('telegrama_recibido_id');
            $table->unsignedBigInteger('envio_id');
            $table->boolean('recibido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegramas_recibidos');
    }
};
