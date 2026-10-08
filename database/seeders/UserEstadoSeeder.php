<?php

namespace Database\Seeders;

use App\Models\UsuarioEstado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserEstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Usuario Admin
        UsuarioEstado::create([
            'id_user' => 2,
            'id_estado' => 1,
        ]);
        UsuarioEstado::create([
            'id_user' => 3,
            'id_estado' => 1,
        ]);
            UsuarioEstado::create([
            'id_user' => 4,
            'id_estado' => 1,
        ]);
            UsuarioEstado::create([
            'id_user' => 5,
            'id_estado' => 1,
        ]);
        UsuarioEstado::create([
            'id_user' => 6,
            'id_estado' => 1,
        ]);
        UsuarioEstado::create([
        'id_user' => 7,
        'id_estado' => 1,
        ]);
        UsuarioEstado::create([
            'id_user' => 8,
            'id_estado' => 1,
        ]);
        UsuarioEstado::create([
            'id_user' => 9,
            'id_estado' => 3,
        ]);
    }
}