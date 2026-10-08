<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TipoRemitenteTelegrama; 
use App\Models\LugarEmisionTelegrama; 

class TelegramasCatalogosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. PRIMERA LISTA: Tipos de Remitente
        $datosRemitentes = [
            ['nombre' => 'Defensor Publico'],
            ['nombre' => 'Defensor Privado'],
            ['nombre' => 'Tercer Interesado'],
            ['nombre' => 'Victima'],
            ['nombre' => 'Fiscal del Ministerio Publico'],
            ['nombre' => 'Juez'],
        ];

        foreach ($datosRemitentes as $dato) {
            TipoRemitenteTelegrama::firstOrCreate(
                ['nombre' => $dato['nombre']] 
            );
        }

        // 2. SEGUNDA LISTA: Lugares de Emisión
        $datosLugares = [
            ['nombre' => 'Circuito Judicial'],
            ['nombre' => 'Tribunal'],
        ];

        foreach ($datosLugares as $dato) {
            LugarEmisionTelegrama::firstOrCreate(
                ['nombre' => $dato['nombre']]
            );
        }
    }
}