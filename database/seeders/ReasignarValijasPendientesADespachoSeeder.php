<?php

namespace Database\Seeders;

use App\Models\Saca;
use App\Services\DespachoService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Reasigna las valijas AUN NO DESPACHADAS que cuelgan de despachos del modelo
 * viejo (sin `oficina_destino_id`) a despachos bien formados del par
 * (origen, destino) real de cada valija.
 *
 * ## Por que
 *
 * Antes del 23-jun-2026 el correlativo del despacho era por oficina de ORIGEN,
 * sin destino: un mismo despacho acumulaba valijas hacia rutas distintas. Con la
 * logica nueva la salida se hace POR DESPACHO -- `agregarDespacho()` mete todas
 * sus valijas al carrito y `create()` las despacha al unico destino elegido en
 * pantalla.
 *
 * Sin esta reasignacion, despachar uno de esos despachos mandaria decenas de
 * valijas hacia un destino equivocado, sin ningun aviso. Ver
 * docs/notas/plan-guias-historicas-por-manifiesto.md
 *
 * ## Alcance
 *
 * Solo las valijas PENDIENTES: las que no figuran en ningun manifiesto, es decir
 * que nunca salieron. Las ya despachadas no se tocan -- su despacho viejo es un
 * registro historico y su informacion real vive en los manifiestos.
 *
 * ## Idempotencia
 *
 * El filtro exige que el despacho actual tenga `oficina_destino_id` NULL. Tras
 * la primera ejecucion las valijas cuelgan de despachos con destino, asi que una
 * segunda pasada no encuentra nada.
 */
class ReasignarValijasPendientesADespachoSeeder extends Seeder
{
    public function run(): void
    {
        $pendientes = $this->valijasPendientes();

        if ($pendientes->isEmpty()) {
            $this->command->warn('Sin cambios: no hay valijas pendientes en despachos sin destino.');
            return;
        }

        $this->command->info("Valijas a reasignar: {$pendientes->count()}");

        // Sin destino propio no hay par (origen, destino) que resolver. En el
        // analisis previo no habia ninguna, pero se comprueba para no crear
        // despachos mal formados si el dato cambia.
        $sinDestino = $pendientes->whereNull('oficina_destino_id');

        if ($sinDestino->isNotEmpty()) {
            $this->command->error("ABORTADO: {$sinDestino->count()} valija(s) sin oficina_destino_id propio.");
            $this->command->error('Codigos: ' . $sinDestino->pluck('codigo_saca')->take(10)->implode(', '));
            return;
        }

        $servicio = app(DespachoService::class);
        $despachosPorPar = [];
        $reasignadas = 0;

        // Una transaccion por par (origen, destino): resolverDespachoActivo() usa
        // lockForUpdate y debe ejecutarse dentro de una transaccion.
        foreach ($pendientes->groupBy(fn($s) => $s->oficina_id . '-' . $s->oficina_destino_id) as $par => $valijas) {
            $primera = $valijas->first();

            DB::transaction(function () use ($servicio, $primera, $valijas, $par, &$despachosPorPar, &$reasignadas) {
                $despachoId = $servicio->resolverDespachoActivo(
                    (int) $primera->oficina_id,
                    (int) $primera->oficina_destino_id
                );

                Saca::whereIn('saca_id', $valijas->pluck('saca_id'))
                    ->update(['numero_despacho_id' => $despachoId]);

                $despachosPorPar[$par] = $despachoId;
                $reasignadas += $valijas->count();
            });
        }

        $this->command->info("Reasignadas: {$reasignadas} valijas en " . count($despachosPorPar) . ' despachos.');

        $this->resumen();
    }

    /**
     * Valijas que cuelgan de un despacho sin destino y que nunca han salido (no
     * figuran en ningun manifiesto).
     */
    private function valijasPendientes()
    {
        return DB::table('sacas as s')
            ->join('numeros_despacho_oficina as nd', 'nd.numero_despacho_id', '=', 's.numero_despacho_id')
            ->whereNull('nd.oficina_destino_id')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('manifiestos_paquetes')
                    ->whereColumn('manifiestos_paquetes.saca_id', 's.saca_id');
            })
            ->select('s.saca_id', 's.codigo_saca', 's.oficina_id', 's.oficina_destino_id')
            ->get();
    }

    /** Estado tras la reasignacion, para verificar de un vistazo. */
    private function resumen(): void
    {
        $quedan = $this->valijasPendientes()->count();

        $enDespachoConDestino = DB::table('sacas as s')
            ->join('numeros_despacho_oficina as nd', 'nd.numero_despacho_id', '=', 's.numero_despacho_id')
            ->whereNotNull('nd.oficina_destino_id')
            ->count();

        $this->command->info("Pendientes restantes en despachos sin destino: {$quedan} (debe ser 0)");
        $this->command->info("Valijas en despachos con destino: {$enDespachoConDestino}");
    }
}