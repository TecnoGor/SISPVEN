<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Vehiculos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vehiculo::create([
            'oficina_id' => 10,
            'placa' => "C4R4AS02",
            'color' => "rojo",
            'marca' => "chevrolet",
            'modelo' => "camioneta de carga",
            'tipo_vehiculo_id' => 1,
            'año' => 2024,
            'num_poliza' => 2401122,
            'fecha_vencimiento' => "2025-01-20",
            'capacidad_carga' => 1500,
            'Activo' => true,
        ]);

        Vehiculo::create([
            'oficina_id' => 6,
            'placa' => "PL4C4XD",
            'color' => "azul",
            'marca' => "chevrolet",
            'modelo' => "camion",
            'tipo_vehiculo_id' => 1,
            'año' => 2024,
            'num_poliza' => 4521477,
            'fecha_vencimiento' => "2025-09-28",
            'capacidad_carga' => 1500,
            'Activo' => true,
        ]);
    }
}
