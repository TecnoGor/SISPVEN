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
        Schema::create('sacas', function (Blueprint $table) {
            $table->id('saca_id');
            $table->foreignId('tipo_saca_id')->references('tipo_saca_id')->on('tipos_sacas');
            $table->foreignId('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreignId('usuario_id')->references('id')->on('users');
            $table->foreignId('oficina_destino_id')->references('oficina_id')->on('oficinas');
            $table->string("peso")->nullable();
            $table->string("codigo_saca");
            $table->boolean('devolucion')->default(false);
            $table->boolean("cerrado");
            $table->string("numero_precinto")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sacas', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['tipo_saca_id']);
            $table->dropForeign(['oficina_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['oficina_destino_id']);
        });

        Schema::dropIfExists('sacas');
    }
};
