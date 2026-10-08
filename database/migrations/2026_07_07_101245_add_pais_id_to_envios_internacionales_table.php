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
        Schema::table('envios_internacionales', function (Blueprint $table) {
            $table->unsignedBigInteger('pais_id')->nullable();
            $table->foreign('pais_id')->references('pais_id')->on('paises');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios_internacionales', function (Blueprint $table) {
            $table->dropForeign(['pais_id']);
            $table->dropColumn('pais_id');
        });
    }
};
