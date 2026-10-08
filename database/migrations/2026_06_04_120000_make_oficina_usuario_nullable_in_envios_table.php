<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hace nullable `envios.oficina_id` y `envios.usuario_id`.
     *
     * Motivo: los telegramas creados desde la app móvil (servicio_id = 3) no
     * tienen oficina asignada al consignarse, y su usuario vive en
     * `sispven_app.usuarios` (IDs disjuntos de `users`), por lo que usuario_id
     * debe quedar NULL. Ver CLAUDE.md y TelegramaFlutterController::consignar.
     *
     * Solo se relaja la restricción NOT NULL — los FK a `oficinas` y `users`
     * se conservan intactos (un FK admite NULL). El sistema web sigue enviando
     * valores reales en ambas columnas, así que no cambia su comportamiento.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE envios ALTER COLUMN oficina_id DROP NOT NULL');
        DB::statement('ALTER TABLE envios ALTER COLUMN usuario_id DROP NOT NULL');
    }

    public function down(): void
    {
        // Nota: revertir fallará si ya existen telegramas móviles con NULL
        // en estas columnas. Limpiar/asignar valores antes de hacer rollback.
        DB::statement('ALTER TABLE envios ALTER COLUMN oficina_id SET NOT NULL');
        DB::statement('ALTER TABLE envios ALTER COLUMN usuario_id SET NOT NULL');
    }
};
