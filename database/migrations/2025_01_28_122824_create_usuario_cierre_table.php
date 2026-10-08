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
        Schema::create('usuario_cierre', function (Blueprint $table) {
            $table->id('usuario_cierre_id');
            $table->integer('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario_cierre', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });

        Schema::dropIfExists('usuario_cierre');
    }
};
