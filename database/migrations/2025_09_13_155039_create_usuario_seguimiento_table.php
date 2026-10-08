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
        Schema::create('usuario_seguimiento', function (Blueprint $table) {
            $table->id('usuario_seguimiento_id');
            $table->foreignId('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('accion');
            $table->string('descripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_seguimiento');
    }
};