<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dispositivos registrados por la app móvil para envío de notificaciones
 * push vía Firebase Cloud Messaging.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cartero_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('fcm_token', 255);
            $table->string('platform', 20); // android | ios
            $table->string('app_version', 30)->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'fcm_token']);
            $table->index('fcm_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cartero_devices');
    }
};
