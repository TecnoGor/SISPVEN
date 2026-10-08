<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicado_destinatario', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('comunicado_id');
            $table->foreign('comunicado_id')->references('comunicado_id')->on('comunicados')->onDelete('cascade');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('estatus_id')->default(1);
            $table->foreign('estatus_id')->references('id')->on('estatus_comunicaciones')->onDelete('restrict');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicado_destinatario');
    }
};
