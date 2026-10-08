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
        Schema::create('envios_exporta_facil', function (Blueprint $table) {
            $table->id('envio_exporta_facil_id');
            $table->unsignedBigInteger('envio_id');
            $table->string('clase_correo');
            $table->string('estado_dest');
            $table->string('ciudad_dest');
            $table->string('parroquia_dest');
            $table->integer('codigo_postal_dest');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
