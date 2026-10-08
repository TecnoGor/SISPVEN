<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposOficinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipos_oficinas')->insert([

            ['nombre' => 'OPT (Pequeña)', 'activo' => 'true'],
            ['nombre' => 'OPT (Mediana)', 'activo' => 'true'],
            ['nombre' => 'OPT (Grande)', 'activo' => 'true'],
            ['nombre' => 'COP', 'activo' => 'true'],
            ['nombre' => 'CENTRALIZADORA', 'activo'=> 'true'],
            ['nombre' => 'CPI', 'activo' => 'true'],
            ['nombre' => 'EXTERNA', 'activo' => 'true'],
        ]);
    }
}
