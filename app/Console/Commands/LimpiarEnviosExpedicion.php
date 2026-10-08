<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Vacía la tabla envios_expedicion.
 *
 * Hasta ahora RegistrarEntradaCOP insertaba una fila en envios_expedicion por cada
 * envío que entraba en CPC (oficina 10), sin que nadie lo mandara a Expedición. Esa
 * inserción automática ya se eliminó del código, pero las filas que dejó siguen ahí:
 * envíos que figuran "en Expedición" sin haber pasado nunca por el botón Aceptar.
 *
 * Además de ensuciar la pantalla, esas filas bloquean el flujo correcto: la bandeja
 * "Por aceptar" descarta los envíos que ya tienen fila activa, así que un envío
 * heredado del atajo sale del almacén y no aparece en ninguna parte para aceptarlo.
 *
 * Por eso se vacía la tabla completa y no solo lo que dejó el atajo: a partir de
 * cero, cada envío entra a Expedición únicamente cuando alguien lo despacha desde
 * Almacén o desde Unidad de Análisis y un operador lo acepta.
 *
 * Ninguna otra tabla apunta a envios_expedicion (no es destino de ninguna FK), así
 * que el borrado no arrastra datos de otras tablas ni rompe integridad referencial.
 *
 * Uso:
 *   php artisan expedicion:limpiar --dry-run   # solo informa, no escribe
 *   php artisan expedicion:limpiar             # pide confirmación y borra
 *   php artisan expedicion:limpiar --force     # borra sin preguntar (despliegues)
 */
class LimpiarEnviosExpedicion extends Command
{
    protected $signature = 'expedicion:limpiar
                            {--dry-run : Muestra qué haría sin escribir nada}
                            {--force : No pedir confirmación (para despliegues desatendidos)}';

    protected $description = 'Vacía envios_expedicion, heredada de la inserción automática al entrar en CPC';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $resumen = DB::table('envios_expedicion')
            ->selectRaw('count(*) AS total')
            ->selectRaw('count(*) FILTER (WHERE estatus) AS activas')
            ->selectRaw('count(*) FILTER (WHERE NOT estatus) AS inactivas')
            ->selectRaw('count(*) FILTER (WHERE "Salida" IS NOT NULL) AS con_salida')
            ->first();

        if ((int) $resumen->total === 0) {
            $this->info('La tabla envios_expedicion ya está vacía. No hay nada que hacer.');
            return self::SUCCESS;
        }

        // Filas que sí llegaron por el flujo correcto: tienen un encaminamiento de
        // ENTRADA EN EXPEDICION (24 bulto, 36 EMS, 40 Iposplus, 42 genérico).
        $conEntradaFormal = DB::table('envios_expedicion as x')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('envios_encaminamiento as ee')
                    ->whereColumn('ee.envio_id', 'x.envio_id')
                    ->whereIn('ee.estatus_id', [24, 36, 40, 42]);
            })
            ->count();

        $this->info(($dryRun ? '[DRY-RUN] ' : '') . 'Contenido actual de envios_expedicion:');
        $this->table(
            ['Total', 'Activas', 'Inactivas', 'Con Salida', 'Con entrada formal'],
            [[$resumen->total, $resumen->activas, $resumen->inactivas, $resumen->con_salida, $conEntradaFormal]]
        );

        if ($conEntradaFormal > 0) {
            $this->warn("Atención: {$conEntradaFormal} fila(s) sí pasaron por el flujo de aceptación.");
            $this->warn('El vaciado completo también las elimina.');
        }

        if ($dryRun) {
            $this->newLine();
            $this->info("[DRY-RUN] Se borrarían {$resumen->total} fila(s). No se escribió nada.");
            return self::SUCCESS;
        }

        if (!$this->option('force')
            && !$this->confirm("¿Borrar las {$resumen->total} fila(s) de envios_expedicion?", false)) {
            $this->info('Cancelado. No se borró nada.');
            return self::SUCCESS;
        }

        // DELETE y no TRUNCATE: respeta la transacción, permitiendo revertir si algo
        // falla. La secuencia del id no se reinicia a propósito, para que los ids
        // nuevos no colisionen con los que aparezcan en logs o reportes anteriores.
        $borradas = DB::transaction(fn() => DB::table('envios_expedicion')->delete());

        $this->info("Listo: {$borradas} fila(s) eliminadas de envios_expedicion.");

        return self::SUCCESS;
    }
}