<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Medidas extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('medidas')->insert([

            ['nombre' => 'Gramos', 'activo' => true, "created_at" => now()],
            ['nombre' => 'Kilos', 'activo' => true, "created_at" => now()],
        ]);
    }
}
