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
        Schema::table('telegramas_recibidos', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_remitente')->nullable();
            $table->unsignedBigInteger('lugar_emision_rem')->nullable();
            $table->unsignedBigInteger('sitio_especifico_emision')->nullable();
            $table->unsignedBigInteger('circuito_judicial_dest')->nullable();
            $table->text('contenido_telegrama')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telegramas_recibidos', function (Blueprint $table) {
            //
        });
    }
};
