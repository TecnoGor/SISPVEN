<?php

namespace Database\Seeders;

use App\Models\TipoSaca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LlenarPivoteSacas extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoSaca::whereNotNull('servicio_id')->get()->each(function ($t) {
            DB::table('tipo_saca_servicio')->insertOrIgnore([
                'tipo_saca_id' => $t->tipo_saca_id,
                'servicio_id'  => $t->servicio_id,
            ]);
        });
    }
}
