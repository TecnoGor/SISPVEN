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
        Schema::create('discapacidades_persona', function (Blueprint $table) {
            $table->id('discapacidad_persona_id');
            $table->unsignedBigInteger('empleado_id')->nullable();
            $table->unsignedBigInteger('grupo_familiar_id')->nullable();
            $table->unsignedBigInteger('tipo_discapacidad_id');
            $table->string('discapacidad_detalle');
            $table->timestamps();

            $table->foreign('empleado_id')->references('empleado_id')->on('empleados');
            $table->foreign('grupo_familiar_id')->references('grupo_familiar_id')->on('grupo_familiar');
            $table->foreign('tipo_discapacidad_id')->references('tipo_discapacidad_id')->on('tipos_discapacidades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discapacidades_persona');
    }
};
