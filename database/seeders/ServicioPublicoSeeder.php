<?php

namespace Database\Seeders;

use App\Models\ServicioPublico;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServicioPublicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            'Electricidad',
            'Agua',
            'Aseo',
            'Internet',
        ];

        foreach ($servicios as $serv) {
            ServicioPublico::firstOrCreate(['nombre' => $serv]);
        }
    }
}
