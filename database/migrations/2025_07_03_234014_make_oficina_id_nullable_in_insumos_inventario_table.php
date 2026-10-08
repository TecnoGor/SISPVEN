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
        Schema::table('insumos_inventario', function (Blueprint $table) {
            
            $table->dropForeign(['oficina_id']);

            $table->unsignedBigInteger('oficina_id')->nullable()->change();

            $table->foreign('oficina_id')->references('oficina_id')->on('oficinas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insumos_inventario', function (Blueprint $table) {
            //
        });
    }
};
