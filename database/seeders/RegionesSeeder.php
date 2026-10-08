<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('regiones')->insert([

            ["pais_id" => 90, "nombre" => "Capital", "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "nombre" => "Andina", "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "nombre" => "Central", "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "nombre" => "Centro Llano", "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "nombre" => "Occidental", "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "nombre" => "Oriental", "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "nombre" => "Vargas", "activo" => true, "created_at" => now()],
        ]);
    }
}
