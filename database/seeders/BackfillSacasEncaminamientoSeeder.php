<?php

namespace Database\Seeders;

use App\Models\Saca;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Crea el movimiento inicial de encaminamiento (ESTATUS_CREADA) de las valijas
 * ABIERTAS que no tienen ninguno.
 *
 * POR QUE HACE FALTA
 * ------------------
 * Los listados de valijas pasan a resolver la ubicacion desde
 * `sacas_encaminamiento` en vez de `sacas.oficina_id`. La tabla de
 * encaminamiento se creo en junio de 2026, asi que las valijas anteriores no
 * tienen ningun registro: sin este backfill desaparecerian de la vista de sus
 * operadores el dia del despliegue.
 *
 * SUPUESTO EXPLICITO (el punto de mayor riesgo)
 * ---------------------------------------------
 * Se asume que estas valijas NO se movieron de la oficina donde se crearon, y
 * por eso se las ubica en `sacas.oficina_id`.
 *
 * El supuesto no se puede verificar directamente —no hay registro—, pero se
 * apoya en dos argumentos:
 *   1. Operativo: una valija se cierra antes de despacharse, y estas estan
 *      abiertas.
 *   2. Empirico: `sacas_encaminamiento` escribe desde junio de 2026. Si alguna
 *      se hubiera despachado despues, tendria registro de transito. Ninguna lo
 *      tiene.
 *
 * Si el supuesto falla en algun caso, esa valija queda ubicada en la oficina
 * equivocada. El seeder es idempotente y el efecto es reversible borrando los
 * movimientos que crea (ver ALCANCE abajo para identificarlos).
 *
 * ALCANCE
 * -------
 * Solo toca valijas ABIERTAS sin ningun encaminamiento. Las CERRADAS se dejan
 * fuera a proposito: ya no se operan, y quedan localizables en el listado de
 * "creadas por mi oficina". Decision pendiente de revisar con operaciones.
 *
 * Los movimientos creados aqui son identificables por tener `usuario_id` NULL
 * junto a `saca_estatus_id` = ESTATUS_CREADA, ya que el flujo normal siempre
 * registra el usuario que crea la valija.
 */
class BackfillSacasEncaminamientoSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotente: solo valijas abiertas que NO tienen ningun movimiento.
        // Al correrlo de nuevo, las ya backfilleadas quedan excluidas por el
        // whereNotExists, de modo que no se duplican.
        $sacas = DB::table('sacas')
            ->where('cerrado', false)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('sacas_encaminamiento as se')
                    ->whereColumn('se.saca_id', 'sacas.saca_id');
            })
            ->select('saca_id', 'oficina_id', 'created_at')
            ->orderBy('saca_id')
            ->get();

        if ($sacas->isEmpty()) {
            $this->command->info('No hay valijas abiertas pendientes de backfill.');
            return;
        }

        $filas = $sacas->map(fn($s) => [
            'saca_id'            => $s->saca_id,
            'oficina_id'         => $s->oficina_id,   // se asume que sigue ahi
            'oficina_externa_id' => null,             // no vino de ningun sitio: es su creacion
            'saca_estatus_id'    => Saca::ESTATUS_CREADA,
            'usuario_id'         => null,             // marca de que el registro es del backfill
            'created_at'         => $s->created_at,   // fecha real de creacion de la valija
            'updated_at'         => $s->created_at,
        ])->all();

        // En bloques para no armar un INSERT gigante ni agotar los placeholders
        // del driver si el volumen crece.
        foreach (array_chunk($filas, 500) as $bloque) {
            DB::table('sacas_encaminamiento')->insert($bloque);
        }

        $this->command->info('Backfill completado: ' . count($filas) . ' valija(s) abiertas ubicadas en su oficina de creacion.');
    }
}
