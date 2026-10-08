<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OficinaPersonalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $data = [
            ['oficina_id' => 1, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 1, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 1, 'rol_id' => 9, 'cantidad_max' => 1],
            
            ['oficina_id' => 2, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 2, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 2, 'rol_id' => 9, 'cantidad_max' => 1],
            
            ['oficina_id' => 3, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 3, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 3, 'rol_id' => 9, 'cantidad_max' => 1],
            
            ['oficina_id' => 4, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 4, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 4, 'rol_id' => 9, 'cantidad_max' => 1],
            
            ['oficina_id' => 5, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 5, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 5, 'rol_id' => 9, 'cantidad_max' => 1],


            // COP
            ['oficina_id' => 6, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 6, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 6, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 7, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 7, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 7, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 8, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 8, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 8, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 9, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 9, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 9, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 10, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 10, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 10, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 11, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 11, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 11, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 12, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 12, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 12, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 13, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 13, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 13, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 14, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 14, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 14, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 15, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 15, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 15, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 16, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 16, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 16, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 17, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 17, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 17, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 18, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 18, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 18, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 19, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 19, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 19, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 20, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 20, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 20, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 21, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 21, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 21, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 22, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 22, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 22, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 23, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 23, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 23, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 24, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 24, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 24, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 25, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 25, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 25, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 26, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 26, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 26, 'rol_id' => 9, 'cantidad_max' => 1],

            ['oficina_id' => 27, 'rol_id' => 11, 'cantidad_max' => 1],
            ['oficina_id' => 27, 'rol_id' => 12, 'cantidad_max' => 1],
            ['oficina_id' => 27, 'rol_id' => 9, 'cantidad_max' => 1],

        ];
        $roles = [4, 5, 6, 7, 8, 9]; // Roles dinámicos
        for ($oficina_id = 28; $oficina_id <= 192; $oficina_id++) {
            foreach ($roles as $rol_id) {
                $data[] = [
                    'oficina_id' => $oficina_id,
                    'rol_id' => $rol_id,
                    'cantidad_max' => 1,
                ];
            }
        }

        // Inserción en la base de datos
        DB::table('oficina_personal')->insert($data);

    }
}
