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
        Schema::create('facturacion_detalles', function (Blueprint $table) {
            $table->id('facturacion_detalle_id');
            $table->unsignedBigInteger('servicio_id');
            $table->unsignedBigInteger('facturacion_id');
            $table->decimal('monto');
            $table->timestamps();

            $table->foreign('facturacion_id')->references('facturacion_id')->on('facturaciones');
            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturacion_detalles', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['servicio_id']);
            $table->dropForeign(['facturacion_id']);
        });

        Schema::dropIfExists('facturacion_detalles');
    }
};
