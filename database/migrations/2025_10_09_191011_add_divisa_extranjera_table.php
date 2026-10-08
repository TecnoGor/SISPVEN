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
        Schema::table('contratos_corporativos', function (Blueprint $table) {
            $table->unsignedBigInteger('parametro_id')->nullable();
            $table->float('monto_divisa', 9,2)->nullable();

            $table->foreign('parametro_id')->references('parametro_id')->on('parametro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
