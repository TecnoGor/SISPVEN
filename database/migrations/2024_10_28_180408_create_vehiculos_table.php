<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculosTable extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id('vehiculo_id');
            
            // Proveedor
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->foreign('proveedor_id')->references('proveedor_id')->on('proveedores');

            // Oficina
            $table->unsignedBigInteger('oficina_id')->nullable();
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas');
            
            // Chofer
            $table->unsignedBigInteger('chofer_id')->nullable();
            $table->foreign('chofer_id')->references('chofer_id')->on('choferes')->nullOnDelete();

            // Usuario
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            
            // Tipo de vehículo
            $table->unsignedBigInteger('tipo_vehiculo_id')->nullable();
            $table->foreign('tipo_vehiculo_id')->references('tipo_vehiculo_id')->on('tipo_vehiculo')->nullOnDelete();

            // Detalles del vehículo
            $table->string('placa');
            $table->string('color');
            $table->string('marca');
            $table->string('modelo');
            $table->string('año');
            $table->string('num_poliza');
            $table->date('fecha_vencimiento');
            $table->string('capacidad_carga');
            $table->boolean('Activo')->default(true);
            $table->string('imagen')->nullable(); // Nueva columna para la ruta de la imagen
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
}
