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
        Schema::create('alianzas', function (Blueprint $table) {
            $table->id('alianza_id');
            $table->unsignedBigInteger('cliente_corporativo_id');
            $table->unsignedBigInteger('tipo_alianza_id');
            $table->float('porcentaje', 9, 2);
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('cliente_corporativo_id')->references('cliente_corporativo_id')->on('clientes_corporativos');
            $table->foreign('tipo_alianza_id')->references('tipo_alianza_id')->on('tipos_alianzas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alianzas');
    }
};
