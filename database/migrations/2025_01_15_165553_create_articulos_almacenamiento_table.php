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
        Schema::create('articulos_almacenamiento', function (Blueprint $table) {
            $table->id('articulo_alamacenamiento_id');
            $table->unsignedBigInteger('contrato_almacenamiento_id');
            $table->string('articulo');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos_alamacenamiento', function (Blueprint $table) {
            $table->dropForeign(['contrato_almacenamiento_id']);
        });

        Schema::dropIfExists('articulos_almacenamiento');
    }
};
