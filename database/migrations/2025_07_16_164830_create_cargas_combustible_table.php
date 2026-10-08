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
        Schema::create('cargas_combustible', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehiculo_id');
            $table->date('fecha');
            $table->decimal('litros', 10, 2);
            $table->decimal('costo_total', 10, 2);
            $table->unsignedInteger('kilometraje')->nullable();
            $table->timestamps();

            $table->foreign('vehiculo_id')->references('vehiculo_id')->on('vehiculos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargas_combustible');
    }
};
