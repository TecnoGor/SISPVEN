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
        Schema::create('almacen_avisos', function (Blueprint $table) {
            $table->id('almacen_aviso_id');
            $table->unsignedBigInteger('envio_almacen_id');
            $table->unsignedBigInteger('envio_id');
            $table->unsignedBigInteger('usuario_id');
            $table->timestamps();

            $table->foreign('envio_almacen_id')->references('envio_almacen_id')->on('envios_almacen');
            $table->foreign('envio_id')->references('envio_id')->on('envios');
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('almacen_avisos', function (Blueprint $table) {
            $table->dropForeign(['almacen_envo_id']);
            $table->dropForeign(['envio_id']);
            $table->dropForeign(['usuario_id']);
        });

        Schema::dropIfExists('almacen_avisos');
    }
};
