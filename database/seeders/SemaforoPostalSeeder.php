<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SemaforoPostalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('oficinas_semaforo_postal')->insert([

            ["oficina_id" => '1', "condicion" => 'Arrendada', "fecha_inicio" => '2024-01-01', "fecha_fin" => '024-01-31'],
            ["oficina_id" => '2', "condicion" => 'Propia Ipostel', "fecha_inicio" => Null, "fecha_fin" => Null],
        ]);
    }
}
