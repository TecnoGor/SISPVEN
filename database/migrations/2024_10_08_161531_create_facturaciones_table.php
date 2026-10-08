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
        Schema::create('facturaciones', function (Blueprint $table) {
            $table->id('facturacion_id');
            $table->unsignedBigInteger('oficina_id');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('tipo_documento');
            $table->string('documento');
            $table->string('direccion');
            $table->float('monto_total',8,2);
            $table->float('iva',8,2);
            $table->timestamps();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturaciones', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['oficina_id']);
        });

        Schema::dropIfExists('facturaciones');
    }
};
