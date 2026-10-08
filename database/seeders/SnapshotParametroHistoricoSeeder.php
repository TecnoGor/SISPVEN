<?php

namespace Database\Seeders;

use App\Models\Parametro;
use App\Models\ParametroHistorico;
use Illuminate\Database\Seeder;

class SnapshotParametroHistoricoSeeder extends Seeder
{
    public function run(): void
    {
        $divisas = Parametro::all();

        foreach ($divisas as $divisa) {
            // Solo si aún no tiene ningún registro histórico
            $existe = ParametroHistorico::where('parametro_id', $divisa->parametro_id)->exists();

            if (!$existe) {
                ParametroHistorico::create([
                    'parametro_id'   => $divisa->parametro_id,
                    'valor_anterior' => null,
                    'valor_nuevo'    => $divisa->valor,
                    'usuario_id'     => null, // sistema
                    'fecha_cambio'   => $divisa->updated_at ?? $divisa->created_at ?? now(),
                ]);
            }
        }
    }
}
