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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('empleado_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('oficina_id');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('tipo_documento');
            $table->string('documento');
            $table->string('correo');
            $table->string('telefono');
            $table->string('telefono_secundario')->nullable();
            $table->string('telefono_emergencia')->nullable();
            $table->date('fecha_nacimiento');
            $table->boolean('genero');
            $table->unsignedBigInteger('nacionalidad');
            $table->unsignedBigInteger('estado_civil');
            $table->date('fecha_ingreso');

            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('nacionalidad')->references('nacionalidad_id')->on('nacionalidad');
            $table->foreign('estado_civil')->references('estado_civil_id')->on('estados_civiles');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nuevos_ingresos');
    }
};
