<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Carga los 10 motivos oficiales de devolución que muestra la pestaña 7.3
 * de la app móvil. Idempotente: usa upsert por codigo.
 */
class MotivosDevolucionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $motivos = [
            ['codigo' => 'closed_home',          'nombre' => 'DOMICILIO CERRADO'],
            ['codigo' => 'unknown_address',      'nombre' => 'DIRECCIÓN DESCONOCIDA'],
            ['codigo' => 'changed_residence',    'nombre' => 'CAMBIÓ DOMICILIARIO'],
            ['codigo' => 'does_not_work_there',  'nombre' => 'NO TRABAJA ALLÍ'],
            ['codigo' => 'not_claimed',          'nombre' => 'NO RETIRADO'],
            ['codigo' => 'deceased',             'nombre' => 'FALLECIDO'],
            ['codigo' => 'moved',                'nombre' => 'MUDADO'],
            ['codigo' => 'demolished',           'nombre' => 'DEMOLIDO'],
            ['codigo' => 'refused',              'nombre' => 'RECHAZADO'],
            ['codigo' => 'insufficient_address', 'nombre' => 'DIRECCIÓN INSUFICIENTE'],
        ];

        foreach ($motivos as $motivo) {
            DB::table('motivos_devolucion')->updateOrInsert(
                ['codigo' => $motivo['codigo']],
                [
                    'nombre' => $motivo['nombre'],
                    'activo' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
