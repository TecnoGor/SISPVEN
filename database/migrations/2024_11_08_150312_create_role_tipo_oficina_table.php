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
        Schema::create('role_tipo_oficina', function (Blueprint $table) {
            $table->id('role_tipo_oficina_id');
            $table->string('rol_id');
            $table->unsignedBigInteger('tipo_oficina_id');
            $table->timestamps();

            $table->foreign('tipo_oficina_id')->references('tipo_oficina_id')->on('tipos_oficinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_tipo_oficina', function (Blueprint $table) {

            $table->fropForeign(['tipo_oficina_id']);
        });

        Schema::dropIfExists('role_tipo_oficina');
    }
};
