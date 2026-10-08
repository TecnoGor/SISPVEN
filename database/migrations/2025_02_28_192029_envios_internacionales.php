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
        Schema::create('envios_internacionales', function (Blueprint $table) {
            $table->id('envio_internacional_id');
            $table->unsignedBigInteger('envio_id');
            $table->string('tipo_envio')->nullable();
            $table->string('pais')->nullable();
            $table->boolean('lista_correo')->default('false');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
