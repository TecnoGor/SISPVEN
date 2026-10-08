<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('documentos')->insert([

            ["tipo" => 'V', "descripcion" => 'venezolano', "activo" => 1, "created_at" => now()],
            ["tipo" => 'E', "descripcion" => 'extranjero', "activo" => 1, "created_at" => now()],
            ["tipo" => 'J', "descripcion" => 'juridico', "activo" => 1, "created_at" => now()],
            ["tipo" => 'C', "descripcion" => 'comuna', "activo" => 1, "created_at" => now()],
            ["tipo" => 'G', "descripcion" => 'gubernamental', "activo" => 1, "created_at" => now()],
            ["tipo" => 'P', "descripcion" => 'pasaporte', "activo" => 1, "created_at" => now()],
        ]);
    }
}
