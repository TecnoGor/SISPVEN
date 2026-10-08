<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunicados', function (Blueprint $table) {
            if (!Schema::hasColumn('comunicados', 'seguimiento')) {
                $table->boolean('seguimiento')->default(false);
            }
            if (!Schema::hasColumn('comunicados', 'correcciones')) {
                $table->unsignedSmallInteger('correcciones')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('comunicados', function (Blueprint $table) {
            $table->dropColumn(['seguimiento', 'correcciones']);
        });
    }
};
