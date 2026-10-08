<?php

namespace Database\Seeders;

use App\Models\Rutas;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Ruta extends Seeder
{

    public function run(): void
    {
        Rutas::create([
            'distancia' => 10,
            'ruta' => "Cop San Martin a Opt Chacao",
            'oficina_id_origen' => 10,
            'oficina_id_destino' => 38,
            'tiempo' => 2,
            'activo' => true,
        ]);

        Rutas::create([
            'distancia' => 10,
            'ruta' => "Cop San Martin a Cop Barinas",
            'oficina_id_origen' => 10,
            'oficina_id_destino' => 6,
            'tiempo' => 4,
            'activo' => true,
        ]);

        Rutas::create([
            'distancia' => 2,
            'ruta' => "Cop Barinas a Opt Barinas",
            'oficina_id_origen' => 6,
            'oficina_id_destino' => 78,
            'tiempo' => 1,
            'activo' => true,
        ]);
    }
}
