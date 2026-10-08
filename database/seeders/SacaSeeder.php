<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SacaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sacas')->insert([

            ["tipo_saca_id" => 1, "oficina_id" => 1, "usuario_id" => 1, "oficina_destino_id" => 2, "cerrado" => false, "devolucion" => false, "codigo_saca" => "OF1-US1-TS1-OD2-0001", "created_at" => now() ],
            ["tipo_saca_id" => 2, "oficina_id" => 1, "usuario_id" => 1, "oficina_destino_id" => 2, "cerrado" => false, "devolucion" => false, "codigo_saca" => "OF1-US1-TS2-OD2-0001", "created_at" => now() ],
            ["tipo_saca_id" => 3, "oficina_id" => 1, "usuario_id" => 1, "oficina_destino_id" => 2, "cerrado" => false, "devolucion" => false, "codigo_saca" => "OF1-US1-TS3-OD2-0001", "created_at" => now() ],
            ["tipo_saca_id" => 4, "oficina_id" => 1, "usuario_id" => 1, "oficina_destino_id" => 2, "cerrado" => false, "devolucion" => false, "codigo_saca" => "OF1-US1-TS4-OD2-0001", "created_at" => now() ],
            ["tipo_saca_id" => 5, "oficina_id" => 1, "usuario_id" => 1, "oficina_destino_id" => 2, "cerrado" => false, "devolucion" => false, "codigo_saca" => "OF1-US1-TS5-OD2-0001", "created_at" => now() ],
            ["tipo_saca_id" => 2, "oficina_id" => 2, "usuario_id" => 2, "oficina_destino_id" => 1, "cerrado" => false, "devolucion" => false, "codigo_saca" => "OF1-US1-TS2-OD2-0002", "created_at" => now() ],
            ["tipo_saca_id" => 5, "oficina_id" => 2, "usuario_id" => 1, "oficina_destino_id" => 1, "cerrado" => false, "devolucion" => false, "codigo_saca" => "OF1-US1-TS5-OD2-0002", "created_at" => now() ],

        ]);
    }
}
