<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Incidencia extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('incidencias')->insert([
            ['incidencia' => 'Envio sin incluir en la Guia de Despacho', "created_at" => now()],
            ['incidencia' => 'Sobre de un despacho incompleto', "created_at" => now()],
            ['incidencia' => 'Sobre de un despacho mal encaminado', "created_at" => now()],
            ['incidencia' => 'Sobre deteriorado y/o dañado', "created_at" => now()],
            ['incidencia' => 'Sobre humedo', "created_at" => now()],
            ['incidencia' => 'Avería o Expoliación en el sobre.', "created_at" => now()],
            ['incidencia' => 'Precinto roto y/o sin precinto', "created_at" => now()],
            ['incidencia' => 'Sin registrar', "created_at" => now()],
            ['incidencia' => 'Roto', "created_at" => now()],
            ['incidencia' => 'Deteriorado', "created_at" => now()],
            ['incidencia' => 'Con signos de violación', "created_at" => now()],
            ['incidencia' => 'Mal Acondicionado', "created_at" => now()],
            ['incidencia' => 'Mal Encaminado', "created_at" => now()],
            ['incidencia' => 'Con la Guía de Consignación mal elaborada', "created_at" => now()],
            ['incidencia' => 'Sin Guía de Consignación', "created_at" => now()],
            ['incidencia' => 'Devuelto sin colocar el motivo', "created_at" => now()],
            ['incidencia' => 'Con diferencia de peso', "created_at" => now()],
            ['incidencia' => 'Otra irregularidad no incluida en el listado.', "created_at" => now()],
        ]);
    }
}
