<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use App\Models\OficinaVehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OficinasVehiculosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehiculos = Vehiculo::all();

        foreach ($vehiculos as $vehiculo) {
            OficinaVehiculo::firstOrCreate(
                ['oficina_id' => $vehiculo->oficina_id, 'vehiculo_id' => $vehiculo->vehiculo_id],
                ['activo' => $vehiculo->Activo]
            ); 
        }
    }
}
