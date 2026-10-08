<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConvertirContratoDivisaSeeder extends Seeder
{
    /**
     * Tasas históricas USD → Bs.
     * Formato: 'YYYY-MM-DD' => valor
     * El seeder buscará la tasa más reciente anterior o igual a la fecha del contrato.
     */
    private $tasasHistoricas = [
        '2025-08-05' => 128.08,
        '2026-03-04' => 427.39,
        '2026-04-14' => 477.98,
        '2026-04-27' => 484.61,
        '2026-04-28' => 485.58,
        '2026-04-29' => 486.51,
        '2026-05-04' => 489.55,
        '2026-05-05' => 490.04,
    ];

    public function run(): void
    {
        $this->convertirCasoA();
        $this->convertirCasoB();
    }

    /**
     * CASO A: contratos creados como USD legacy.
     * Tienen monto_divisa y parametro_id llenos. Ya tenemos el USD original.
     */
    private function convertirCasoA(): void
    {
        $contratos = DB::table('contratos_corporativos')
            ->whereNotNull('monto_divisa')
            ->whereNotNull('parametro_id')
            // Filtro idempotente: solo procesar si tarifa != monto_divisa (señal de no convertido)
            ->whereColumn('tarifa', '!=', 'monto_divisa')
            ->get();

        foreach ($contratos as $contrato) {
            DB::table('contratos_corporativos')
                ->where('contrato_corporativo_id', $contrato->contrato_corporativo_id)
                ->update(['tarifa' => $contrato->monto_divisa]);

            $cuotaDivisa = bcdiv((string)$contrato->monto_divisa, '12', 2);
            DB::table('contratos_corporativos_detalles')
                ->where('contrato_corporativo_id', $contrato->contrato_corporativo_id)
                ->update(['cuota' => $cuotaDivisa]);

            $this->command->info("Caso A — Contrato {$contrato->contrato_corporativo_id} convertido a USD {$contrato->monto_divisa}");
        }
    }

    /**
     * CASO B: contratos creados en Bs puros.
     * Sin parametro_id ni monto_divisa. Convertir usando tasa histórica.
     */
    private function convertirCasoB(): void
    {
        $contratos = DB::table('contratos_corporativos')
            ->whereNull('monto_divisa')
            ->whereNull('parametro_id')
            ->get();

        foreach ($contratos as $contrato) {
            $fechaContrato = Carbon::parse($contrato->created_at)->toDateString();
            $tasa = $this->tasaEnFecha($fechaContrato);

            if (!$tasa) {
                $this->command->warn("Contrato {$contrato->contrato_corporativo_id} ({$fechaContrato}): no hay tasa histórica. Saltado.");
                continue;
            }

            $tarifaUSD = bcdiv((string)$contrato->tarifa, (string)$tasa, 2);

            DB::table('contratos_corporativos')
                ->where('contrato_corporativo_id', $contrato->contrato_corporativo_id)
                ->update([
                    'tarifa' => $tarifaUSD,
                    'monto_divisa' => $tarifaUSD,
                    'parametro_id' => 1,
                ]);

            $detalles = DB::table('contratos_corporativos_detalles')
                ->where('contrato_corporativo_id', $contrato->contrato_corporativo_id)
                ->get();

            foreach ($detalles as $detalle) {
                DB::table('contratos_corporativos_detalles')
                    ->where('contrato_corporativo_detalle_id', $detalle->contrato_corporativo_detalle_id)
                    ->update(['cuota' => bcdiv((string)$detalle->cuota, (string)$tasa, 2)]);
            }

            $this->command->info("Caso B — Contrato {$contrato->contrato_corporativo_id} ({$fechaContrato}) convertido con tasa {$tasa}: USD {$tarifaUSD}");
        }
    }

    /**
     * Devuelve la tasa más reciente anterior o igual a la fecha dada.
     */
    private function tasaEnFecha(string $fecha): ?float
    {
        // Ordenar las claves descendentemente y buscar la más cercana sin pasarse
        $fechas = array_keys($this->tasasHistoricas);
        rsort($fechas);

        foreach ($fechas as $f) {
            if ($f <= $fecha) {
                return $this->tasasHistoricas[$f];
            }
        }

        return null; // ninguna fecha del array es <= a la solicitada
    }
}
