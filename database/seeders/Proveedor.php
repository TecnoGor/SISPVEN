<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Proveedor extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('proveedores')->insert([
            [
                'representante_legal' => 'Jesus',
                'telefono' => '04127754123',  
                'cedula' => '16355421',        
                'correo' => 'jesus.ipostel@gmail.com',
                'rif' => 'G200000430',        
                'razon_social' => 'INSTITUTO POSTAL TELEGRAFICO DE VENEZUELA',
                'direccion_fiscal' => 'Av. Principal de El Silencio, Bloque 1, PB, Local 12. Frente a la Plaza O leary, El Silencio,',
                'propio' => true,
                'activo' => true,
                'created_at' => now(),
            ],
        ]);
        
    }
}
