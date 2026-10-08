<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clientes')->insert([
            ['nombre' => 'Carlos', 'apellido' => 'Perez', 'tipo_documento' => 'E', 'numero_documento' => '12345678', 'telefono' => '04242745643', 'correo' => 'carlos@gmail.com', "created_at" => now()],
            ['nombre' => 'Sara', 'apellido' => 'Montana', 'tipo_documento' => 'V', 'numero_documento' => '87654321', 'telefono' => '04243846643', 'correo' => 'sara@gmail.com', "created_at" => now()],
        ]);
    }
}
