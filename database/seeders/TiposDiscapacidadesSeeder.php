<?php

namespace Database\Seeders;

use App\Models\TipoDiscapacidad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposDiscapacidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discapacidades = [
            'Física / Motora',
            'Visual',
            'Auditiva',
            'Intelectual',
            'Psicosocial / Mental',
            'Sensorial',
            'Visceral / Orgánica',
        ];

        foreach ($discapacidades as $discapacidad) {
            TipoDiscapacidad::firstOrCreate([
                'nombre' => $discapacidad,
            ]);
        }
    }
}
