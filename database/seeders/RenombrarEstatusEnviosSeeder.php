<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RenombrarEstatusEnviosSeeder extends Seeder
{
    /**
     * Normaliza los nombres de envios_estatus:
     *
     *  1. Renombra los estatus de entrada de OPT y COP para que digan "Llegada"
     *     en vez de "Entrada".
     *  2. Pasa todos los nombres a MAYUSCULAS. Los estatus viejos (1-18) estaban
     *     en formato mixto ("Salida de Aduana") mientras que los agregados despues
     *     ya venian en mayusculas ("SALIDA HACIA REZAGO"), de modo que la lista se
     *     veia inconsistente en pantalla.
     *
     * Ambos pasos son idempotentes: el renombrado busca por nombre anterior (no por
     * ID fijo) y la conversion solo toca las filas que aun difieren de su version en
     * mayusculas, asi que reejecutar el seeder no cambia nada.
     */
    public function run(): void
    {
        $this->renombrarEntradasPorLlegadas();
        $this->pasarTodoAMayusculas();
    }

    private function renombrarEntradasPorLlegadas(): void
    {
        $renombrar = [
            'Entrada desde OPT' => 'Llegada desde OPT',
            'Entrada desde COP' => 'Llegada desde COP',
        ];

        foreach ($renombrar as $anterior => $nuevo) {
            $afectados = DB::table('envios_estatus')
                ->where('estatus', $anterior)
                ->update(['estatus' => $nuevo]);

            if ($afectados > 0) {
                $this->command->info("Renombrado: '{$anterior}' -> '{$nuevo}'");
                continue;
            }

            // Puede que ya se haya ejecutado antes (incluida la version ya pasada a
            // mayusculas por este mismo seeder), o que el estatus no exista.
            $yaRenombrado = DB::table('envios_estatus')
                ->whereRaw('UPPER(estatus) = ?', [mb_strtoupper($nuevo, 'UTF-8')])
                ->exists();

            $this->command->warn($yaRenombrado
                ? "Sin cambios: '{$nuevo}' ya estaba renombrado."
                : "Sin cambios: no se encontro el estatus '{$anterior}'.");
        }
    }

    private function pasarTodoAMayusculas(): void
    {
        // La conversion se hace en SQL: UPPER() de Postgres respeta los acentos con
        // UTF-8 ("Envío" -> "ENVÍO"), y el WHERE evita reescribir las filas que ya
        // estaban bien.
        $afectados = DB::table('envios_estatus')
            ->whereRaw('estatus <> UPPER(estatus)')
            ->update(['estatus' => DB::raw('UPPER(estatus)')]);

        $this->command->info($afectados > 0
            ? "Pasados a mayusculas: {$afectados} estatus."
            : 'Sin cambios: todos los estatus ya estaban en mayusculas.');
    }
}
