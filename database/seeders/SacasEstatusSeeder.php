<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SacasEstatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sacas_estatus')->insertOrIgnore([
            ['saca_estatus_id' => 1, 'nombre' => 'Valija Creada'], // nueva valija
            ['saca_estatus_id' => 2, 'nombre' => 'Valija En Tránsito'], // despachada, no recibida aún
            ['saca_estatus_id' => 3, 'nombre' => 'Valija Recibida'],    // recibida en un COP: sigue cerrada y puede reexpedirse
            ['saca_estatus_id' => 4, 'nombre' => 'Valija Abierta'],     // recibida en una OPT: se abrió y liberó sus envíos (estado final)
        ]);
    }
}
