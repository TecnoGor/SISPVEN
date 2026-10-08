<?php

namespace Database\Seeders;

use App\Models\DiaSemana;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DiasSemanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dias = [
            ['dia_semana' => 'Lunes'],
            ['dia_semana' => 'Martes'],
            ['dia_semana' => 'Miércoles'],
            ['dia_semana' => 'Jueves'],
            ['dia_semana' => 'Viernes'],
            ['dia_semana' => 'Sábado'],
            ['dia_semana' => 'Domingo'],
        ];

        // Insertar los días en la tabla dias_semana
        DiaSemana::insert($dias);
    }
}
