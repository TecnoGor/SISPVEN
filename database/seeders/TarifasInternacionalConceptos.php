<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TarifasInternacionalConceptos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('tarifas_internacional_conceptos')->insert([


            ['nombre' => 'Certificado', 'monto' => 316.66,'activo' => true, 'servicios_id' => 16, "created_at" => now()],
            ['nombre' => 'Lista de Correo', 'monto' => 316.66,'activo' => true, 'servicios_id' => 16, "created_at" => now()],
            ['nombre' => 'Almacenaje', 'monto' => 33.62,'activo' => true, 'servicios_id' => 16, "created_at" => now()],
            ['nombre' => 'Cupones Respuesta cambio', 'monto' => 316.66,'activo' => true, 'servicios_id' => 16, "created_at" => now()],
            ['nombre' => 'Peticion de Reexpedicion o Devolucion', 'monto' => 316.66,'activo' => true, 'servicios_id' => 16, "created_at" => now()],
            ['nombre' => 'Presentacion a la Aduana cobrada en origen', 'monto' => 316.66,'activo' => true, 'servicios_id' => 16, "created_at" => now()],
            ['nombre' => 'Presentacion a la Aduana cobrada en destino', 'monto' => 316.66,'activo' => true, 'servicios_id' => 16, "created_at" => now()],
            ['nombre' => 'Entrega al destinatario de un paquete de 500 Grs o mas', 'monto' => 316.66,'activo' => true, 'servicios_id' => 16, "created_at" => now()],
        ]);
    }
}
