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
        Schema::create('grupo_familiar', function (Blueprint $table) {
            $table->id('grupo_familiar_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('parentesco_id');
            $table->boolean('genero');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('fecha_nacimiento')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('documento')->nullable();
            $table->string('telefono')->nullable();
            $table->boolean('trabaja')->default(false);
            $table->boolean('estudia')->default(false);
            $table->unsignedBigInteger('nivel_educativo_id')->nullable();
            $table->boolean('vive_con_empleado')->default(false);
            $table->boolean('tiene_discapacidad')->default(false);
            $table->string('partida_nacimiento')->nullable();


            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            $table->foreign('empleado_id')->references('empleado_id')->on('empleados');
            $table->foreign('parentesco_id')->references('parentesco_id')->on('parentescos');
            $table->foreign('nivel_educativo_id')->references('nivel_educativo_id')->on('niveles_educativos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_familiar');
    }
};
