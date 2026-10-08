<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lugar_emision_telegramas', function (Blueprint $table) {
            $table->bigIncrements('lugar_emision_telegramas_id'); // ID
            $table->string('nombre'); // Nombre para mostrar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lugar_emision_telegramas');
    }
};
