<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envios_rezago', function (Blueprint $table) {
            $table->id('envio_rezago_id');

            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('envio_id');

            // true  = está físicamente en el almacén de rezago
            // false = ya salió del almacén de rezago
            $table->boolean('estatus')->default(true);

            $table->date('Entrada');
            $table->date('Salida')->nullable();

            // Observaciones libres: usadas para registrar el motivo de cada acción
            // (tanto del ingreso como de la salida).
            $table->text('observaciones')->nullable();

            // Trazabilidad: dos usuarios distintos según la acción
            $table->unsignedBigInteger('usuario_ingreso_id');
            $table->unsignedBigInteger('usuario_salida_id')->nullable();

            $table->timestamps();

            $table->index(['oficina_id', 'estatus']);
            $table->index('Entrada');

            $table->foreign('oficina_id')
                ->references('oficina_id')->on('oficinas');

            $table->foreign('envio_id')
                ->references('envio_id')->on('envios');

            $table->foreign('usuario_ingreso_id')
                ->references('id')->on('users');

            $table->foreign('usuario_salida_id')
                ->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envios_rezago');
    }
};
