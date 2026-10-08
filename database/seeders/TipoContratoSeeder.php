<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoContratoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipos_contratos')->insert([

            ['descripcion' => 'Peso', 'created_at' => now()],
            ['descripcion' => 'Envios', 'created_at' => now()],
            ['descripcion' => 'Mixto', 'created_at' => now()],
        ]);
    }
}
