<?php

namespace Database\Seeders;

use App\Models\EstadoCivil;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoCivilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados_civiles = [
            'Soltero',
            'Casado',
            'Divorciado',
            'Viudo',
        ];

        foreach ($estados_civiles as $estado_civil) {
            EstadoCivil::firstOrCreate([
                'nombre' => $estado_civil,
            ]);
        }
    }
}
