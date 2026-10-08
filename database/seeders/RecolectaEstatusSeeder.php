<?php

namespace Database\Seeders;

use App\Models\RecolectaEstatus;
use Illuminate\Database\Seeder;

/**
 * Estados del flujo de recolectas. Idempotente por slug (los consumidores
 * referencian por slug, nunca por id).
 */
class RecolectaEstatusSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['slug' => RecolectaEstatus::SOLICITADA, 'nombre' => 'Solicitada', 'orden' => 1],
            ['slug' => RecolectaEstatus::PAGO_REPORTADO, 'nombre' => 'Pago Reportado', 'orden' => 2],
            ['slug' => RecolectaEstatus::PAGO_CONFIRMADO, 'nombre' => 'Pago Confirmado', 'orden' => 3],
            ['slug' => RecolectaEstatus::RECOLECTADA, 'nombre' => 'Recolectada', 'orden' => 4],
            ['slug' => RecolectaEstatus::CANCELADA, 'nombre' => 'Cancelada', 'orden' => 5],
            ['slug' => RecolectaEstatus::RECHAZADA, 'nombre' => 'Rechazada', 'orden' => 6],
        ];

        foreach ($estados as $estado) {
            RecolectaEstatus::firstOrCreate(
                ['slug' => $estado['slug']],
                ['nombre' => $estado['nombre'], 'orden' => $estado['orden'], 'activo' => true]
            );
        }
    }
}
