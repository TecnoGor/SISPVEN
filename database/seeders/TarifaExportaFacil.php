<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TarifaExportaFacil extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tarifas_exporta_facil')->insert([

            ["pais_id" => 67,  "monto" => 7.45,  "activo" => true, "medida_id" => 1, "created_at" => now()],
            ["pais_id" => 171, "monto" => 9.00,  "activo" => true, "medida_id" => 1, "created_at" => now()],
            ["pais_id" => 65,  "monto" => 7.06,  "activo" => true, "medida_id" => 1, "created_at" => now()],
            ["pais_id" => 176, "monto" => 7.30,  "activo" => true, "medida_id" => 1, "created_at" => now()],       
        ]);
    }
}
