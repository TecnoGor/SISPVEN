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
        Schema::create('facturacion_servicios', function (Blueprint $table) {
            $table->id('facturacion_servicio_id');
            $table->unsignedBigInteger('servicio_id');
            $table->unsignedBigInteger('referencia_id');
            $table->string('tipo_documento');
            $table->string('documento');
            $table->decimal('monto_subtotal', 10, 2);
            $table->decimal('monto_iva', 10, 2)->default(0);
            $table->decimal('monto_total', 10, 2);
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('usuario_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturacion_servicios');
    }
};
