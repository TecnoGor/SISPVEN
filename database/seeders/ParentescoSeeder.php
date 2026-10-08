<?php

namespace Database\Seeders;

use App\Models\Parentesco;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParentescoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentescos = [
            'Abuelo/a',
            'Padre',
            'Madre',
            'Hermano/a',
            'Hijo/a',
            'Hijastro/a',
            'Conyugue',
            'Otro',
        ];

        foreach ($parentescos as $parentesco) {
            Parentesco::firstOrCreate([
                'nombre' => $parentesco,
            ]);
        }
    }
}
