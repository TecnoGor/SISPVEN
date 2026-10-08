<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Espejo de la configuración 7.4 que define la obligatoriedad de los campos
 * del destinatario en la app móvil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipient_requirement_config', function (Blueprint $table) {
            $table->string('field_key', 40)->primary();
            $table->string('label', 60);
            $table->boolean('is_required')->default(true);
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipient_requirement_config');
    }
};
