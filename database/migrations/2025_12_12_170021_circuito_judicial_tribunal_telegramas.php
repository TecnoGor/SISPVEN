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
        Schema::create('circuito_judicial_tribunal_telegramas', function (Blueprint $table) {

            // ID - Clave Primaria
            $table->bigIncrements('circuito_judicial_tribunal_telegramas_id');

            // FK que enlaza con lugar_emision_telegramas
            $table->unsignedBigInteger('lugar_emision_telegramas_id');

            // Nombre del Tribunal o Circuito Judicial
            $table->string('nombre');

            // Campo para el estado Activo/Inactivo
            $table->boolean('activo')->default(true); // Por defecto, es activo

            $table->timestamps(); // Columnas created_at y updated_at

            // Definición de la clave foránea
            $table->foreign('lugar_emision_telegramas_id')
                ->references('lugar_emision_telegramas_id')
                ->on('lugar_emision_telegramas')
                ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circuito_judicial_tribunal_telegramas');
    }
};
