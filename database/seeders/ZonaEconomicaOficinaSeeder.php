<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZonaEconomicaOficinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Actualización masiva de todos los registros en la tabla oficinas
        \Illuminate\Support\Facades\DB::table('oficinas')->update([
            'zona_economica_especial' => false
        ]);
    }
}
