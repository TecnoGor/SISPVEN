<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Quita la restriccion NOT NULL de comunicados.destinatario_id.
 *
 * Contexto: la migracion original create_comunicados_table definia un unico
 * destinatario por comunicado (columna destinatario_id NOT NULL). Despues se
 * cambio el modelo a multiples destinatarios via la tabla pivote
 * comunicado_destinatario, y la columna se elimino de esa migracion.
 *
 * Como Laravel no re-ejecuta migraciones ya aplicadas, los entornos que corrieron
 * la version vieja conservan la columna con su NOT NULL. Ningun codigo la escribe
 * (no esta en el $fillable de App\Models\Comunicado), asi que todo INSERT en
 * comunicados falla con: "null value in column destinatario_id violates not-null".
 *
 * Es idempotente: si la columna no existe (entornos donde la BD se recreo con la
 * version actualizada de create_comunicados_table) no hace nada.
 *
 * No borra la columna ni sus datos: solo levanta la restriccion. Las filas
 * historicas conservan su valor y la FK a users sigue activa. Se usa SQL directo
 * en vez de ->change() para no alterar la definicion de la FK existente.
 */
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('comunicados', 'destinatario_id')) {
            DB::statement('ALTER TABLE comunicados ALTER COLUMN destinatario_id DROP NOT NULL');
        }
    }

    public function down(): void
    {
        // Solo se puede restaurar el NOT NULL si ninguna fila quedo con NULL,
        // de lo contrario Postgres rechaza el ALTER. Si hay nulos, se deja como
        // esta en vez de reventar el rollback.
        if (! Schema::hasColumn('comunicados', 'destinatario_id')) {
            return;
        }

        $hay_nulos = DB::table('comunicados')->whereNull('destinatario_id')->exists();

        if (! $hay_nulos) {
            DB::statement('ALTER TABLE comunicados ALTER COLUMN destinatario_id SET NOT NULL');
        }
    }
};
