<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TipoVehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipo_vehiculo')->insert([

            ["tipo" => "Camion","created_at" => now()],
            ["tipo" => "Autobús","created_at" => now()],
            ["tipo" => "Grúa","created_at" => now()],
            ["tipo" => "Sedan","created_at" => now()],
            ["tipo" => "Camioneta de cabina","created_at" => now()],
            ["tipo" => "Techo duro","created_at" => now()],
            ["tipo" => "Ambulancia","created_at" => now()],
            ["tipo" => "Moto","created_at" => now()],
        ]);
    }
}
