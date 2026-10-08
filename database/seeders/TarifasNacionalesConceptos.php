<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TarifasNacionalesConceptos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tarifas_nacionales_conceptos')->insert([

            //APARTADO POSTAL
            ['nombre' => 'Persona Natural', 'monto' => 1633.66, 'exclusion' => null, 'activo' => true, 'servicios_id' => 2, "created_at" => now()],
            ['nombre' => 'Persona Juridica', 'monto' => 3705.61, 'exclusion' => null, 'activo' => true, 'servicios_id' => 2, "created_at" => now()],

            //TARJETA POSTAL
            ['nombre' => 'Cada Tarjeta', 'monto' => 33.62, 'exclusion' => null, 'activo' => true, 'servicios_id' => 6, "created_at" => now()],

            //TELEGRAMA
            ['nombre' => 'Telegrama Ordinario', 'monto' => 10.34 , 'exclusion' => 5,'activo' => true, 'servicios_id' => 3, "created_at" => now()],
            ['nombre' => 'Telegrama Urgente', 'monto' => 20.35 , 'exclusion' => 4,'activo' => true, 'servicios_id' => 3, "created_at" => now()],
            ['nombre' => 'Copia Certificada (Grupo de 50 palabras o faccion)', 'monto' => 30.69 , 'exclusion' => null, 'activo' => true, 'servicios_id' => 3, "created_at" => now()],
            ['nombre' => 'Peticion de entrega (PC)', 'monto' => 30.69 , 'exclusion' => null, 'activo' => true, 'servicios_id' => 3, "created_at" => now()],
            ['nombre' => 'Telefonograma', 'monto' => 30.69 , 'exclusion' => null, 'activo' => true, 'servicios_id' => 3, "created_at" => now()],

            //PORTA PAGADO
            ['nombre' => 'Costos Administrativos', 'monto' => 771.16 , 'exclusion' => null, 'activo' => true, 'servicios_id' => 4, "created_at" => now()],
            ['nombre' => 'Tramites de Permiso', 'monto' => 771.16 , 'exclusion' => null, 'activo' => true, 'servicios_id' => 4, "created_at" => now()],

            //PORTA A PAGAR
            ['nombre' => 'Costos Administrativos', 'monto' => 771.16 , 'exclusion' => null, 'activo' => true, 'servicios_id' => 5, "created_at" => now()],
            ['nombre' => 'Tramites de Permiso', 'monto' => 771.16 , 'exclusion' => null, 'activo' => true, 'servicios_id' => 5, "created_at" => now()],
            ['nombre' => 'Apartado Interno', 'monto' => 771.16 , 'exclusion' => null, 'activo' => true, 'servicios_id' => 5, "created_at" => now()],

            //SERVICIOS ESPECIALES REGIMEN NACIONAL
            ['nombre' => 'Certificado', 'monto' => 33.62, 'exclusion' => null, 'activo' => true, 'servicios_id' => 7, "created_at" => now()],
            ['nombre' => 'Aviso de Recibo', 'monto' => 33.62, 'exclusion' => null, 'activo' => true, 'servicios_id' => 7, "created_at" => now()],
            ['nombre' => 'Almacenaje, por Dia', 'monto' => 33.62, 'exclusion' => null, 'activo' => true, 'servicios_id' => 7, "created_at" => now()],
            ['nombre' => 'Aviso de Llegada', 'monto' => 33.62, 'exclusion' => null, 'activo' => true, 'servicios_id' => 7, "created_at" => now()],
            ['nombre' => 'Peticion de Reexpedicion', 'monto' => 33.62, 'exclusion' => null, 'activo' => true, 'servicios_id' => 7, "created_at" => now()],
            ['nombre' => 'Peticion de Devolucion o Modificacion de Direccion', 'monto' => 33.62, 'exclusion' => null, 'activo' => true, 'servicios_id' => 7, "created_at" => now()],
            ['nombre' => 'Entrega por taquilla al destinatario de una encomienda de mas de 2kgrs', 'monto' => 33.62, 'exclusion' => null, 'activo' => true, 'servicios_id' => 7, "created_at" => now()],

        ]);
    }
}
