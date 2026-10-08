<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnvioAlmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('envios_almacen')->insert([
            ['oficina_id' => 78, 'envio_id' => 1, 'codigo' => 'OP011CP005000000001', 'saca_id' => null, 'estatus' => true, 'Entrada' => Carbon::create(2025, 2, 17, 0, 0, 0), 
            'Salida' => null,  'created_at' => Carbon::create(2025, 2, 17, 0, 0, 0), 'updated_at' => Carbon::create(2025, 2, 17, 0, 0, 0)],

            ['oficina_id' => 78, 'envio_id' => 2, 'codigo' => 'OP011CP005000000002', 'saca_id' => null, 'estatus' => true, 'Entrada' => Carbon::create(2025, 2, 17, 0, 0, 0), 
            'Salida' => null,  'created_at' => Carbon::create(2025, 2, 17, 0, 0, 0), 'updated_at' => Carbon::create(2025, 2, 17, 0, 0, 0)],
            
        ]);
    }
}
