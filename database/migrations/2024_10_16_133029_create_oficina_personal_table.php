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
        Schema::create('oficina_personal', function (Blueprint $table) {
            $table->id('oficina_personal_id'); // Clave primaria de la tabla
            $table->unsignedBigInteger('oficina_id'); // Relación con oficinas
            $table->unsignedBigInteger('rol_id'); // Relación con roles (Spatie)
            $table->integer('cantidad_max'); // Cantidad máxima
            $table->timestamps();
    
            // Clave foránea con la tabla oficinas
            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas')->onDelete('cascade');
    
            // Clave foránea con la tabla roles de Spatie
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oficina_personal', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la tabla
            $table->dropForeign(['oficina_id']);
        });

        Schema::dropIfExists('oficina_personal');
    }
};
