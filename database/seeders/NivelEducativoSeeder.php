<?php

namespace Database\Seeders;

use App\Models\NivelEducativo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NivelEducativoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveles = [
            ['nombre' => 'Sin Estudios'],
            ['nombre' => 'Basico Incompleto'],
            ['nombre' => 'Educación Inicial'],
            ['nombre' => 'Educación Primaria'],
            ['nombre' => 'Bachiller'],
            ['nombre' => 'Educación Media Técnica'],
            ['nombre' => 'Técnico Superior Universitario (TSU)'],
            ['nombre' => 'Universitario'],
            ['nombre' => 'Especialización'],
            ['nombre' => 'Maestría'],
            ['nombre' => 'Doctorado'],
        ];

        foreach ($niveles as $nivel) {
            NivelEducativo::firstOrCreate($nivel);
        }
    }
}
