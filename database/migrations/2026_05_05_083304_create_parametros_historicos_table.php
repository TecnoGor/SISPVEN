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
        Schema::create('parametros_historicos', function (Blueprint $table) {
            $table->id('parametro_historico_id');
            $table->unsignedBigInteger('parametro_id');
            $table->float('valor_anterior', 9, 2)->nullable();
            $table->float('valor_nuevo', 9, 2);
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamp('fecha_cambio');
            $table->timestamps();

            $table->foreign('parametro_id')->references('parametro_id')->on('parametro');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->index(['parametro_id', 'fecha_cambio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros_historicos');
    }
};
