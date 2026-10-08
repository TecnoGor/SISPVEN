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
        Schema::create('choferes', function (Blueprint $table) {
            $table->id('chofer_id'); // ID del chofer
            $table->string('nombre'); // Nombre del chofer
            $table->string('cedula'); // Cédula del chofer
            $table->string('rif'); // RIF del chofer
            $table->string('direccion'); // Dirección del chofer
            $table->string('telefono'); // Teléfono del chofer
            $table->string('correo')->unique(); // Correo del chofer
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->unsignedBigInteger('oficina_id')->nullable(); // Columna para la clave foránea
            $table->boolean('activo')->default(true); // Estado del chofer, por defecto activo

            // Definir la clave foránea
            $table->foreign('proveedor_id')
                ->references('proveedor_id')
                ->on('proveedores')
                ->onDelete('cascade'); // Borrar choferes asociados si el proveedor se elimina

            $table->foreign('oficina_id')
                ->references('oficina_id')
                ->on('oficinas')
                ->onDelete('cascade');
            $table->timestamps(); // Timestamps para created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('choferes', function (Blueprint $table) {
            $table->dropForeign(['proveedor_id']); // Eliminar la clave foránea antes de eliminar la tabla
        });

        Schema::dropIfExists('choferes'); // Eliminar la tabla
    }
};
