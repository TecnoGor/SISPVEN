<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EstatusOficinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('estatus_oficinas')->insert([
            ['estatus' => 'Activa', "created_at" => now()],
            ['estatus' => 'Inoperativa', "created_at" => now()],
            ['estatus' => 'Inactiva', "created_at" => now()],
        ]);
    }
}
