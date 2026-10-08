<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El tipo de saca pasó a relacionarse con sus servicios vía la pivote
     * tipo_saca_servicio. La columna legacy servicio_id ya no se usa al crear
     * tipos nuevos, por lo que debe permitir NULL (antes era NOT NULL y rompía
     * el insert en ParametrosValijas::guardar()).
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE tipos_sacas ALTER COLUMN servicio_id DROP NOT NULL');
    }

    public function down(): void
    {
        // Solo se puede revertir si no quedan filas con servicio_id NULL.
        DB::statement('ALTER TABLE tipos_sacas ALTER COLUMN servicio_id SET NOT NULL');
    }
};
