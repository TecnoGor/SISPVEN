<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS sispven_app');

        // Evitar error en migrate:fresh: el schema externo no se borra
        $exists = DB::select("SELECT to_regclass('sispven_app.usuarios') IS NOT NULL AS existe");
        if ($exists[0]->existe) {
            return;
        }

        Schema::create('sispven_app.usuarios', function (Blueprint $table) {
            $table->id('usuario_id');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo')->nullable()->unique();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('contraseña', 255);
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('cedula');
            $table->string('tipo_documento');
            $table->string('rol')->default('usuario');
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->date('correo_verificado')->nullable();
            $table->string('otp')->nullable();
            $table->string('imagen_perfil')->nullable(); 
            $table->string('imagen_documento')->nullable();
            $table->boolean('identidad_verificada')->default(false);
            $table->timestamps();

            // Índices únicos compuestos
            $table->unique(['cedula', 'tipo_documento'], 'unique_cedula_tipo');
            
            // Índices para mejorar búsquedas
            $table->index('correo');
            $table->index('activo');

            $table->foreign('estado_id')->references('estado_id')->on('public.estados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sispven_app.usuarios');
    }
};
