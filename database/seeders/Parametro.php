<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Parametro extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('parametro')->insert([
        ["nombre" => 'BCV DOLAR',"valor" => 41.13, "activo" => true, "created_at" => now()],
        ["nombre" => 'BCV EURO',"valor" => 37.65, "activo" => true, "created_at" => now()],
        ["nombre" => 'LIBRA ESTERLINA',"valor" => 48.47, "activo" => true, "created_at" => now()],
        ["nombre" => 'RENMINBI CHINO',"valor" => 5.26, "activo" => true, "created_at" => now()],
        ["nombre" => 'YEN JAPONES',"valor" => 0.25, "activo" => true, "created_at" => now()],
        ["nombre" => 'IposPlus',"valor" => 60, "activo" => true, "created_at" => now()],
    ]);
    }
}
