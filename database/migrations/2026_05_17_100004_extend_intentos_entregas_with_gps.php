<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extiende intentos_entregas con columnas opcionales de GPS, motivo y
 * evidencia para soportar los registros enviados desde la app móvil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intentos_entregas', function (Blueprint $table) {
            if (!Schema::hasColumn('intentos_entregas', 'motivo_id')) {
                $table->unsignedBigInteger('motivo_id')->nullable()->after('usuario_id');
                $table->foreign('motivo_id')->references('id')->on('motivos_devolucion')->nullOnDelete();
            }
            if (!Schema::hasColumn('intentos_entregas', 'latitud')) {
                $table->decimal('latitud', 10, 7)->nullable()->after('motivo_id');
            }
            if (!Schema::hasColumn('intentos_entregas', 'longitud')) {
                $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            }
            if (!Schema::hasColumn('intentos_entregas', 'evidencia_id')) {
                $table->unsignedBigInteger('evidencia_id')->nullable()->after('longitud');
                $table->foreign('evidencia_id')->references('evidencia_id')->on('envio_evidencias')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('intentos_entregas', function (Blueprint $table) {
            foreach (['evidencia_id', 'longitud', 'latitud', 'motivo_id'] as $col) {
                if (Schema::hasColumn('intentos_entregas', $col)) {
                    try {
                        $table->dropForeign([$col]);
                    } catch (\Throwable $e) {
                        // No FK; continuar.
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};
