<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TarifasNacionalesRangos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tarifa_nacionales_rangos')->insert([

            //SERVICIO POSTAL UNIVERSAL
            ["desde" => 0.01, "hasta" => 50, "monto" => 33.62, "activo" => true, "medida_id" => 1, "servicios_id" => 1, "created_at" => now()],
            ["desde" => 50.1, "hasta" => 100, "monto" => 67.13, "activo" => true, "medida_id" => 1, "servicios_id" => 1, "created_at" => now()],
            ["desde" => 100.1, "hasta" => 500, "monto" => 95.85, "activo" => true, "medida_id" => 1, "servicios_id" => 1, "created_at" => now()],
            ["desde" => 500.1, "hasta" => 1000, "monto" => 128.00, "activo" => true, "medida_id" => 1, "servicios_id" => 1, "created_at" => now()],
            ["desde" => 1000.1, "hasta" => 2000, "monto" => 221.98, "activo" => true, "medida_id" => 1, "servicios_id" => 1, "created_at" => now()],

            //TARIFAS RECOLECCION A DOMICILIO
            ["desde" => 0.0001, "hasta" => 250, "monto" => 1139, "activo" => true, "medida_id" => 2, "servicios_id" => 8, "created_at" => now()],
            ["desde" => 250.1, "hasta" => 500, "monto" => 2279, "activo" => true, "medida_id" => 2, "servicios_id" => 8, "created_at" => now()],
            ["desde" => 500.1, "hasta" => 750, "monto" => 3419, "activo" => true, "medida_id" => 2, "servicios_id" => 8, "created_at" => now()],
            ["desde" => 750.1, "hasta" => 1000, "monto" => 4558, "activo" => true, "medida_id" => 2, "servicios_id" => 8, "created_at" => now()],
            ["desde" => 1000.1, "hasta" => 1250, "monto" => 5698, "activo" => true, "medida_id" => 2, "servicios_id" => 8, "created_at" => now()],
            ["desde" => 1250.1, "hasta" => 1500, "monto" => 6837, "activo" => true, "medida_id" => 2, "servicios_id" => 8, "created_at" => now()],
            ["desde" => 1500.1, "hasta" => 1750, "monto" => 7978, "activo" => true, "medida_id" => 2, "servicios_id" => 8, "created_at" => now()],
            ["desde" => 1750.1, "hasta" => 2000, "monto" => 9117, "activo" => true, "medida_id" => 2, "servicios_id" => 8, "created_at" => now()],
        ]);
    }
}
