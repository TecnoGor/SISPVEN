<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiciosOperativosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('servicios_operativos')->insert([

            ["servicio_operativo" => 'Enviar', "activo" => true, "created_at" =>now()],
            ["servicio_operativo" => 'Recibir', "activo" => true, "created_at" =>now()],
        ]);
    }
}
