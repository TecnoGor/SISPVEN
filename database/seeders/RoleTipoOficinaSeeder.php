<?php

namespace Database\Seeders;

use App\Models\RoleTipoOficina;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleTipoOficinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $datos = [
        ['rol_id' => 4, 'tipo_oficina_id' => 2],
        ['rol_id' => 4, 'tipo_oficina_id' => 3],
        ['rol_id' => 5, 'tipo_oficina_id' => 1],
        ['rol_id' => 5, 'tipo_oficina_id' => 2],
        ['rol_id' => 5, 'tipo_oficina_id' => 3],
        ['rol_id' => 6, 'tipo_oficina_id' => 2],
        ['rol_id' => 6, 'tipo_oficina_id' => 3],
        ['rol_id' => 7, 'tipo_oficina_id' => 1],
        ['rol_id' => 7, 'tipo_oficina_id' => 2],
        ['rol_id' => 7, 'tipo_oficina_id' => 3],
        ['rol_id' => 8, 'tipo_oficina_id' => 3],
        ['rol_id' => 11, 'tipo_oficina_id' => 4],
        ['rol_id' => 11, 'tipo_oficina_id' => 5],
        ['rol_id' => 12, 'tipo_oficina_id' => 4],
        ['rol_id' => 12, 'tipo_oficina_id' => 5],
        ['rol_id' => 18, 'tipo_oficina_id' => 1],
        ['rol_id' => 18, 'tipo_oficina_id' => 2],
        ['rol_id' => 18, 'tipo_oficina_id' => 3],
        ['rol_id' => 18, 'tipo_oficina_id' => 4],
        ['rol_id' => 18, 'tipo_oficina_id' => 5],
        ['rol_id' => 18, 'tipo_oficina_id' => 6],
        ['rol_id' => 19, 'tipo_oficina_id' => 4],
        ['rol_id' => 9, 'tipo_oficina_id' => 4],
        ['rol_id' => 9, 'tipo_oficina_id' => 1],
        ['rol_id' => 9, 'tipo_oficina_id' => 2],
        ['rol_id' => 9, 'tipo_oficina_id' => 3],
        ['rol_id' => 13, 'tipo_oficina_id' => 1],
        ['rol_id' => 13, 'tipo_oficina_id' => 2],
        ['rol_id' => 13, 'tipo_oficina_id' => 3],
        ['rol_id' => 14, 'tipo_oficina_id' => 1],
        ['rol_id' => 14, 'tipo_oficina_id' => 2],
        ['rol_id' => 14, 'tipo_oficina_id' => 3],
        ['rol_id' => 15, 'tipo_oficina_id' => 1],
        ['rol_id' => 15, 'tipo_oficina_id' => 2],
        ['rol_id' => 15, 'tipo_oficina_id' => 3],
        ['rol_id' => 16, 'tipo_oficina_id' => 1],
        ['rol_id' => 16, 'tipo_oficina_id' => 2],
        ['rol_id' => 16, 'tipo_oficina_id' => 3],
        // ROL DE APERTURADOR DE CPC DISPONIBLE EN LAS OPT  Y EN COP
        ['rol_id' => 20, 'tipo_oficina_id' => 1],
        ['rol_id' => 20, 'tipo_oficina_id' => 2],
        ['rol_id' => 20, 'tipo_oficina_id' => 3],
        ['rol_id' => 20, 'tipo_oficina_id' => 4],
        // ROL DE INSPECTOR POSTAL DISPONIBLE EN LAS OPT, COP Y CENTRALIZADORAS
        ['rol_id' => 21, 'tipo_oficina_id' => 1],
        ['rol_id' => 21, 'tipo_oficina_id' => 2],
        ['rol_id' => 21, 'tipo_oficina_id' => 3],
        ['rol_id' => 21, 'tipo_oficina_id' => 4],
        ['rol_id' => 21, 'tipo_oficina_id' => 5],
        // ROL DE CLASIFICADOR DE BULTOS DISPONIBLE EN LAS OPT Y EN COP
        ['rol_id' => 22, 'tipo_oficina_id' => 1],
        ['rol_id' => 22, 'tipo_oficina_id' => 2],
        ['rol_id' => 22, 'tipo_oficina_id' => 3],
        ['rol_id' => 22, 'tipo_oficina_id' => 4],
        // ROL DE CLASIFICADOR DE EMS DISPONIBLE EN LAS OPT Y EN COP
        ['rol_id' => 23, 'tipo_oficina_id' => 1],
        ['rol_id' => 23, 'tipo_oficina_id' => 2],
        ['rol_id' => 23, 'tipo_oficina_id' => 3],
        ['rol_id' => 23, 'tipo_oficina_id' => 4],
        // ROL DE TELEGRAFISTA DISPONIBLE EN LAS OPT
        ['rol_id' => 24, 'tipo_oficina_id' => 1],
        ['rol_id' => 24, 'tipo_oficina_id' => 2],
        ['rol_id' => 24, 'tipo_oficina_id' => 3],
        // ROL DE SOPORTE DISPONIBLE EN LAS OPT, COP Y CENTRALIZADORAS
        ['rol_id' => 25, 'tipo_oficina_id' => 1],
        ['rol_id' => 25, 'tipo_oficina_id' => 2],
        ['rol_id' => 25, 'tipo_oficina_id' => 3],
        ['rol_id' => 25, 'tipo_oficina_id' => 4],
        ['rol_id' => 25, 'tipo_oficina_id' => 5],
        // ROL DE INSPECTOR POSTAL DISPONIBLE EN LAS OPT, COP Y CENTRALIZADORAS
    ];

        foreach ($datos as $dato) {
            RoleTipoOficina::firstOrCreate(
                ['rol_id' => $dato['rol_id'], 'tipo_oficina_id' => $dato['tipo_oficina_id']],
                ['created_at' => now()]
            );
        }

    }
}
