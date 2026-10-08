<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApartadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oficinas = [
            31 => ['inicio' => 1, 'final' => 100], 32 => ['inicio' => 1, 'final' => 200], 34 => ['inicio' => 16000, 'final' => 16351],
            35 => ['inicio' => 1, 'final' => 120], 36 => ['inicio' => 50011, 'final' => 52149], 37 => ['inicio' => 1, 'final' => 300],
            38 => ['inicio' => 1, 'final' => 100], 40 => ['inicio' => 1, 'final' => 300], 41 => ['inicio' => 1, 'final' => 85],
            43 => ['inicio' => 1, 'final' => 100], 45 => ['inicio' => 1, 'final' => 250], 46 => ['inicio' => 1, 'final' => 150],
            47 => ['inicio' => 1, 'final' => 50], 49 => ['inicio' => 1, 'final' => 200], 50 => ['inicio' => 1, 'final' => 100],
            54 => ['inicio' => 1, 'final' => 300], 57 => ['inicio' => 1, 'final' => 500], 59 => ['inicio' => 1, 'final' => 50],
            62 => ['inicio' => 4016, 'final' => 5123], 66 => ['inicio' => 1, 'final' => 300], 68 => ['inicio' => 1, 'final' => 200],
            70 => ['inicio' => 1, 'final' => 100], 71 => ['inicio' => 1, 'final' => 100], 73 => ['inicio' => 1, 'final' => 100],
            74 => ['inicio' => 1, 'final' => 400], 77 => ['inicio' => 1, 'final' => 100], 78 => ['inicio' => 1, 'final' => 100],
            83 => ['inicio' => 1, 'final' => 100], 84 => ['inicio' => 1, 'final' => 120], 85 => ['inicio' => 1, 'final' => 50],
            87 => ['inicio' => 1, 'final' => 50], 89 => ['inicio' => 4500, 'final' => 4599], 90 => ['inicio' => 2200, 'final' => 2999],
            91 => ['inicio' => 1, 'final' => 150], 95 => ['inicio' => 1, 'final' => 50], 96 => ['inicio' => 1, 'final' => 250],
            98 => ['inicio' => 3001, 'final' => 3999], 99 => ['inicio' => 1, 'final' => 100], 100 => ['inicio' => 1, 'final' => 100],
            103 => ['inicio' => 1, 'final' => 50], 104 => ['inicio' => 18200, 'final' => 18300], 108 => ['inicio' => 18200, 'final' => 18300],
            110 => ['inicio' => 18200, 'final' => 18300], 111 => ['inicio' => 18200, 'final' => 18300], 112 => ['inicio' => 1, 'final' => 50],
            113 => ['inicio' => 1, 'final' => 120], 116 => ['inicio' => 160, 'final' => 210], 117 => ['inicio' => 1, 'final' => 50],
            118 => ['inicio' => 1, 'final' => 50], 119 => ['inicio' => 1, 'final' => 1130], 120 => ['inicio' => 4000, 'final' => 3050],
            121 => ['inicio' => 3000, 'final' => 4050], 127 => ['inicio' => 1, 'final' => 100], 128 => ['inicio' => 1, 'final' => 105],
            130 => ['inicio' => 1, 'final' => 50], 137 => ['inicio' => 1, 'final' => 50], 138 => ['inicio' => 1, 'final' => 50],
            140 => ['inicio' => 1, 'final' => 791], 143 => ['inicio' => 1, 'final' => 100], 144 => ['inicio' => 1, 'final' => 100],
            145 => ['inicio' => 1, 'final' => 100], 146 => ['inicio' => 1, 'final' => 150], 147 => ['inicio' => 1, 'final' => 800],
            153 => ['inicio' => 1, 'final' => 500], 169 => ['inicio' => 1, 'final' => 60], 179 => ['inicio' => 1, 'final' => 50],
            189 => ['inicio' => 1, 'final' => 50], 190 => ['inicio' => 1, 'final' => 300], 191 => ['inicio' => 1, 'final' => 50],

        ];

        foreach ($oficinas as $oficinaId => $rango) {
            for ($i = $rango['inicio']; $i <= $rango['final']; $i++) {
                DB::table('codigos_apartados')->insert([
                    'apartado' => $i,
                    'oficina_id' => $oficinaId,
                    'operativo' => true,
                    'activo' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
