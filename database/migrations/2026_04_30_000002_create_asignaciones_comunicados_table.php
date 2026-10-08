<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones_comunicados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();

            $table->unsignedBigInteger('emisor_id');
            $table->foreign('emisor_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('analista_id');
            $table->foreign('analista_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('asunto_instruccion');
            $table->text('detalle_instruccion');
            $table->string('tipo_documento_esperado')->nullable();
            $table->timestamp('fecha_limite')->nullable();

            $table->string('estatus')->default('Pendiente');

            $table->unsignedBigInteger('comunicado_generado_id')->nullable();
            $table->foreign('comunicado_generado_id')
                ->references('comunicado_id')
                ->on('comunicados')
                ->onDelete('set null');

            $table->timestamps();

            $table->index(['analista_id', 'estatus']);
            $table->index(['emisor_id', 'estatus']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_comunicados');
    }
};
