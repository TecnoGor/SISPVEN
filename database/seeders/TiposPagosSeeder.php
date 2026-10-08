<?php

namespace Database\Seeders;

use App\Models\TipoPago;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TiposPagosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pagos = [

            ['nombre' => 'Tarjeta de Debito', 'activo' => true, "created_at" => now()],
            ['nombre' => 'Tarjeta de Credito', 'activo' => true, "created_at" => now()],
            ['nombre' => 'Transferencia Bancaria', 'activo' => true, "created_at" => now()],
            ['nombre' => 'Pago Movil', 'activo' => true, "created_at" => now()],
            ['nombre' => 'BioPago', 'activo' => true, "created_at" => now()],
            ['nombre' => 'Corporativo', 'activo' => true, "created_at" => now()],
            ['nombre' => 'Efectivo', 'activo' => true, "created_at" => now()],
        ];

        foreach ($pagos as $pago) {
            TipoPago::firstOrCreate(
                ['nombre' => $pago['nombre']],
                ['activo' => $pago['activo'], 'created_at' => $pago['created_at']]
            );
        }
    }
}
