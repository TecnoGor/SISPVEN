<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Almacena las evidencias (foto / firma) capturadas en la app móvil
 * con su firma digital (hash + transaction_id + GPS).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envio_evidencias', function (Blueprint $table) {
            $table->id('evidencia_id');
            $table->unsignedBigInteger('envio_id')->nullable();
            $table->unsignedBigInteger('envio_almacen_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('tipo', 20); // photo | signature
            $table->string('ruta_archivo', 500);
            $table->string('hash_sha256', 64);
            $table->string('transaction_id', 64)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->decimal('precision_gps', 8, 2)->nullable();
            $table->timestamp('capturada_en')->nullable();
            $table->timestamps();

            $table->foreign('envio_id')->references('envio_id')->on('envios')->nullOnDelete();
            $table->foreign('envio_almacen_id')->references('envio_almacen_id')->on('envios_almacen')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users');

            $table->index('envio_id');
            $table->index('envio_almacen_id');
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envio_evidencias');
    }
};
