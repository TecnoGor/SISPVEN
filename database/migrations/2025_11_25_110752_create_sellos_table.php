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
        Schema::create('sellos', function (Blueprint $table) {
            $table->id('sello_id');
            $table->string('nombre');
            $table->unsignedBigInteger('serie_filatelia_id');
            $table->float('coste', 9, 2);
            $table->boolean('activo');
            $table->timestamps();

            $table->foreign('serie_filatelia_id')->references('serie_filatelia_id')->on('series_filatelias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellos');
    }
};
