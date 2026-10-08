<?php

namespace Database\Seeders;

use App\Models\EntidadBancaria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntidadesBancariasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entidades = [
            ["nombre" => "BANCO DE VENEZUELA", "codigo" => "0102"],
            ["nombre" => "BANCAMIGA BANCO UNIVERSAL", "codigo" => "0172"],
            ["nombre" => "VENEZOLANA DE CREDITO", "codigo" => "0104"],
            ["nombre" => "MERCANTIL", "codigo" => "0105"],
            ["nombre" => "PROVINCIAL", "codigo" => "0108"],
            ["nombre" => "BANCARIBE", "codigo" => "0114"],
            ["nombre" => "BANCO EXTERIOR", "codigo" => "0115"],
            ["nombre" => "BANCO CARONÌ", "codigo" => "0128"],
            ["nombre" => "BANESCO", "codigo" => "0134"],
            ["nombre" => "BANCO SOFITASA", "codigo" => "0137"],
            ["nombre" => "BANCO PLAZA", "codigo" => "0138"],
            ["nombre" => "BANGENTE", "codigo" => "0146"],
            ["nombre" => "FONDO COMUN", "codigo" => "0151"],
            ["nombre" => "100% BANCO", "codigo" => "0156"],
            ["nombre" => "DELSUR", "codigo" => "0157"],
            ["nombre" => "BANCO DEL TESORO", "codigo" => "0163"],
            ["nombre" => "BANCRECER", "codigo" => "0168"],
            ["nombre" => "R4", "codigo" => "0169"],
            ["nombre" => "BANCO ACTIVO", "codigo" => "0171"],
            ["nombre" => "BANPLUS", "codigo" => "0174"],
            ["nombre" => "BANCO DIGITAL DE LOS TRABAJADORES", "codigo" => "0175"],
            ["nombre" => "BANFANB", "codigo" => "0177"],
            ["nombre" => "N58 BANCO DIGITAL", "codigo" => "0178"],
            ["nombre" => "BANCO NACIONAL DE CREDITO", "codigo" => "0191"],
            ["nombre" => "INSTITUTO MUNICIPAL DE CREDITO POPULAR", "codigo" => "0601"],
        ];

        foreach ($entidades as $entidad) {
            EntidadBancaria::firstOrCreate([
                'nombre' => $entidad['nombre'],
                'codigo' => $entidad['codigo'],
            ]);
        }
    }
}
