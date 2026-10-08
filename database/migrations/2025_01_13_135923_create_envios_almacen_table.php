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
        Schema::create('envios_almacen', function (Blueprint $table) {
            $table->id('envio_almacen_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('envio_id');
            $table->string('codigo')->nullable();
            $table->unsignedBigInteger('saca_id')->nullable();
            $table->boolean('estatus');
            $table->date('Entrada')->nullable();
            $table->date('Salida')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios_almacen', function (Blueprint $table) {
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['envio_id']);
            $table->dropForeign(['saca_id']);
        });

        Schema::dropIfExists('envios_almacen');
    }
};
