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
        Schema::create('registros_entregas', function (Blueprint $table) {
            $table->id('registro_entrega_id'); // Clave primaria autoincremental
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('oficina_id');
            $table->unsignedBigInteger('envio_id');
            $table->unsignedBigInteger('servicio_id')->nullable();
            $table->boolean('nacional?');
            $table->string('codigo_envio'); 
            $table->string('cedula_remitente')->nullable();
            $table->string('nombre_remitente')->nullable();
            $table->float('costo_total')->nullable(); // Corrección de varchar -> string
            $table->float('coste_aviso')->nullable();
            $table->float('coste_almacenaje')->nullable();
            $table->boolean('autorizado')->nullable();
            $table->timestamps();

            // Definir claves foráneas con onDelete('cascade')
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas')->onDelete('cascade');
            $table->foreign('envio_id')->references('envio_id')->on('envios')->onDelete('cascade');
            $table->foreign('servicio_id')->references('servicio_id')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_entregas');
    }
};
