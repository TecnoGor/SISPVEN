<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServicioFlota;
use Illuminate\Support\Facades\DB;

class ServiciosFlotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos predeterminados para la tabla servicios_flota
        $servicios = [
            ['nombre' => 'Mantenimiento Preventivo', 'activo' => true],
            ['nombre' => 'Reparación de Motor', 'activo' => true],
            ['nombre' => 'Cambio de Neumáticos', 'activo' => true],
            ['nombre' => 'Revisión General', 'activo' => true],
            ['nombre' => 'Lavado de Vehículo', 'activo' => false],
        ];

        // Insertar datos en la tabla
        DB::table('servicios_flota')->insert($servicios);
    }
}
