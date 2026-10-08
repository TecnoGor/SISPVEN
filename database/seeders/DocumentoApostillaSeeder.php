<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentoApostilla;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DocumentoApostillaSeeder extends Seeder
{
    public function run(): void
    {
        $doc = [
            ['documento' => 'Titulo Universitario', "created_at" => now()],
            ['documento' => 'Permiso de Trabajo', "created_at" => now()],
            ['documento' => 'Notas Certificadas', "created_at" => now()],
            ['documento' => 'Partida de Nacimiento', "created_at" => now()],
            ['documento' => 'Antecedentes Penales', "created_at" => now()],
        ];

        foreach ($doc as $docu) {
            DocumentoApostilla::firstOrCreate(
                ['documento' => $docu['documento']],
                ['created_at' => now()]
            );
        }
    }
}
