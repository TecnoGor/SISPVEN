<?php

namespace Database\Seeders;

use App\Models\Viaje as ModelsViaje;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Viaje extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelsViaje::create([
            'ruta_id' => 1,
            'codigo' => "NDDQPJ",
            'dia_semana_id' => 7,
            'fecha_salida' => "2025-02-03",
            'dia_semana_id' => 5,
            'fecha_salida' => "2025-02-10",
            'vehiculo_id' => 1,
            'propio' => true,
            'activo' => true,
        ]);

        ModelsViaje::create([
            'ruta_id' => 2,
            'codigo' => "KF0QUY",
            'dia_semana_id' => 2,
            'fecha_salida' => "2025-02-10",
            'vehiculo_id' => 1,
            'propio' => false,
            'activo' => true,
        ]);

        ModelsViaje::create([
            'ruta_id' => 3,
            'codigo' => "9FDSBZ",
            'dia_semana_id' => 3,
            'fecha_salida' => "2025-02-10",
            'vehiculo_id' => 2,
            'propio' => false,
            'activo' => true,
        ]);
    }
}
