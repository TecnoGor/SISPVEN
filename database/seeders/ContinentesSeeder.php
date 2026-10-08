<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContinentesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('continentes')->insert([
            ['nombre' => 'África', 'grupo' => 'C', "created_at" => now()],
            ['nombre' => 'América', 'grupo' => 'A', "created_at" => now()],
            ['nombre' => 'Asia', 'grupo' => 'C', "created_at" => now()],
            ['nombre' => 'Europa', 'grupo' => 'B', "created_at" => now()],
            ['nombre' => 'Oceanía', 'grupo' => 'C', "created_at" => now()],
        ]);
    }
}
