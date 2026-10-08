<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TarifaExpresoBolivariano extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tarifa_expreso_bolivariano')->insert([

            ["desde" => 1, "hasta" => 500, "monto" => 110.59, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Urbano', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 1, "hasta" => 500, "monto" => 150.51, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Intraestatal', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 1, "hasta" => 500, "monto" => 177.63, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Nacional', "servicios_id" => 9, "created_at" => now()],
        
            ["desde" => 501, "hasta" => 2000, "monto" => 194.14, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Urbano', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 501, "hasta" => 2000, "monto" => 246.66, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Intraestatal', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 501, "hasta" => 2000, "monto" => 260.94, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Nacional', "servicios_id" => 9, "created_at" => now()],

            ["desde" => 2001, "hasta" => 5000, "monto" => 313.34, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Urbano', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 2001, "hasta" => 5000, "monto" => 373.59, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Intraestatal', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 2001, "hasta" => 5000, "monto" => 498.67, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Nacional', "servicios_id" => 9, "created_at" => now()],

            ["desde" => 5001, "hasta" => 10000, "monto" => 458.54, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Urbano', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 5001, "hasta" => 10000, "monto" => 554.83, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Intraestatal', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 5001, "hasta" => 10000, "monto" => 633.83, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Nacional', "servicios_id" => 9, "created_at" => now()],

            ["desde" => 10001, "hasta" => 20000, "monto" => 607.41, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Urbano', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 10001, "hasta" => 20000, "monto" => 729.92, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Intraestatal', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 10001, "hasta" => 20000, "monto" => 1023.97, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Nacional', "servicios_id" => 9, "created_at" => now()],

            ["desde" => 20001, "hasta" => 30000, "monto" => 842.47, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Urbano', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 20001, "hasta" => 30000, "monto" => 1058.79, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Intraestatal', "servicios_id" => 9, "created_at" => now()],
            ["desde" => 20001, "hasta" => 30000, "monto" => 1453.20, "activo" => true, "medida_id" => 1, "tipo_expreso" => 'Nacional', "servicios_id" => 9, "created_at" => now()],

        ]);
    }
}