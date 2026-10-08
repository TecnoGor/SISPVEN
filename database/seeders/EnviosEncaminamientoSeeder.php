<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnviosEncaminamientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('envios_encaminamiento')->insert([

            ["envio_id" => 1, "oficina_id" => 1, "usuario_id" => 1, "estatus_id" => 1, "created_at" => now(), "devolucion" => false],
            ["envio_id" => 2, "oficina_id" => 1, "usuario_id" => 1, "estatus_id" => 1, "created_at" => now(), "devolucion" => false],
            ["envio_id" => 3, "oficina_id" => 1, "usuario_id" => 1, "estatus_id" => 1, "created_at" => now(), "devolucion" => false],
            ["envio_id" => 4, "oficina_id" => 1, "usuario_id" => 1, "estatus_id" => 1, "created_at" => now(), "devolucion" => false],
            ["envio_id" => 5, "oficina_id" => 1, "usuario_id" => 1, "estatus_id" => 1, "created_at" => now(), "devolucion" => false],
            ["envio_id" => 6, "oficina_id" => 1, "usuario_id" => 1, "estatus_id" => 1, "created_at" => now(), "devolucion" => false],
            ["envio_id" => 7, "oficina_id" => 1, "usuario_id" => 1, "estatus_id" => 1, "created_at" => now(), "devolucion" => false],
            ["envio_id" => 8, "oficina_id" => 1, "usuario_id" => 1, "estatus_id" => 1, "created_at" => now(), "devolucion" => false],

        ]);
        DB::table('envios_encaminamiento')->insert([

            ["envio_id" => 1, "oficina_id" => 1, "usuario_id" => 1, "estatus_id" => 2, "created_at" => now(), "devolucion" => false]

        ]);
    }
}
