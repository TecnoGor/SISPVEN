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
        Schema::table('registros_apartados', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->float('coste', 9, 2)->nullable();
            
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registros_apartados', function (Blueprint $table) {
            //
        });
    }
};
