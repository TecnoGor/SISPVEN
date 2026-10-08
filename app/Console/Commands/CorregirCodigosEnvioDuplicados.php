<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Corrige los codigo_envio duplicados de la tabla envios.
 *
 * Ninguna fila duplicada es borrable: todas tienen datos hijos (encaminamiento,
 * almacén, y varias con entregas/facturación). Por eso la corrección es por
 * RENOMBRADO: dentro de cada grupo, un envío conserva el código original y los
 * demás reciben un sufijo trazable (-D2, -D3, ...).
 *
 * Regla para elegir quién conserva el código (en este orden):
 *   1. El único entregado, si hay exactamente uno.
 *   2. Si hay varios entregados: el de entrega más antigua (el cliente lo recibió primero).
 *   3. Si ninguno fue entregado: el de más movimientos de encaminamiento.
 *   4. Empate en movimientos: el más antiguo (created_at, y envio_id como desempate final).
 *
 * El código también vive duplicado en envios_almacen.codigo y
 * registros_entregas.codigo_envio, así que se actualizan en la misma transacción.
 */
class CorregirCodigosEnvioDuplicados extends Command
{
    protected $signature = 'envios:corregir-codigos-duplicados
                            {--dry-run : Muestra qué haría sin escribir nada}
                            {--piloto : Solo los grupos que son el MISMO envío y difieren únicamente en mayúsculas/minúsculas}
                            {--codigo=* : Procesar únicamente estos códigos (en mayúsculas)}
                            {--limite= : Procesar como máximo N grupos}';

    protected $description = 'Renombra los codigo_envio duplicados dejando uno original por grupo';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $grupos = $this->gruposDuplicados();

        if ($this->option('piloto')) {
            // Mismo envío (un solo remitente/destinatario/oficina) que solo difiere en el case.
            $grupos = $grupos->where('variantes', '>', 1)->where('combos', 1);
        }

        if ($codigos = $this->option('codigo')) {
            $grupos = $grupos->whereIn('cod', $codigos);
        }

        if ($limite = $this->option('limite')) {
            $grupos = $grupos->take((int) $limite);
        }

        if ($grupos->isEmpty()) {
            $this->info('No hay grupos duplicados que corregir.');
            return self::SUCCESS;
        }

        $this->info(($dryRun ? '[DRY-RUN] ' : '') . "Grupos a procesar: {$grupos->count()}");
        $this->newLine();

        $renombrados = 0;

        foreach ($grupos as $grupo) {
            $filas = $this->filasDelGrupo($grupo->cod);
            $conserva = $this->elegirQueConserva($filas);

            $this->line("<fg=cyan>{$grupo->cod}</> ({$filas->count()} filas) — conserva envio_id={$conserva->envio_id} [{$conserva->motivo}]");

            $sufijo = 1;
            foreach ($filas as $fila) {
                if ($fila->envio_id === $conserva->envio_id) {
                    // El ganador solo se normaliza a mayúsculas si venía en minúsculas.
                    if ($fila->codigo_envio !== $grupo->cod) {
                        $this->line("    envio_id={$fila->envio_id}: {$fila->codigo_envio} → {$grupo->cod} (normalizar)");
                        if (! $dryRun) {
                            $this->aplicarCodigo($fila->envio_id, $grupo->cod);
                        }
                    }
                    continue;
                }

                $sufijo++;
                $nuevo = $this->codigoLibre($grupo->cod, $sufijo);
                $this->line("    envio_id={$fila->envio_id}: {$fila->codigo_envio} → <fg=yellow>{$nuevo}</>");

                if (! $dryRun) {
                    $this->aplicarCodigo($fila->envio_id, $nuevo);
                }
                $renombrados++;
            }
        }

        $this->newLine();
        $this->info(($dryRun ? '[DRY-RUN] Se renombrarían ' : 'Renombrados ') . "{$renombrados} envío(s).");

        return self::SUCCESS;
    }

    /** Grupos con más de una fila, comparando el código en mayúsculas. */
    private function gruposDuplicados()
    {
        return DB::table('envios')
            ->select(
                DB::raw('UPPER(codigo_envio) as cod'),
                DB::raw('COUNT(*) as filas'),
                DB::raw('COUNT(DISTINCT codigo_envio) as variantes'),
                // Distintas combinaciones remitente|destinatario|oficina: 1 = es el mismo envío repetido.
                DB::raw("COUNT(DISTINCT COALESCE(documento_rem,'')||'|'||COALESCE(documento_dest,'')||'|'||oficina_id) as combos")
            )
            ->whereNotNull('codigo_envio')
            ->where('codigo_envio', '<>', '')
            ->groupBy(DB::raw('UPPER(codigo_envio)'))
            ->havingRaw('COUNT(*) > 1')
            ->orderBy(DB::raw('UPPER(codigo_envio)'))
            ->get();
    }

    /** Filas del grupo con las señales necesarias para decidir quién conserva. */
    private function filasDelGrupo(string $codigo)
    {
        return DB::table('envios as e')
            ->leftJoin('registros_entregas as r', 'r.envio_id', '=', 'e.envio_id')
            ->leftJoin('envios_encaminamiento as ee', 'ee.envio_id', '=', 'e.envio_id')
            ->whereRaw('UPPER(e.codigo_envio) = ?', [$codigo])
            ->groupBy('e.envio_id', 'e.codigo_envio', 'e.created_at')
            ->select(
                'e.envio_id',
                'e.codigo_envio',
                'e.created_at',
                DB::raw('COUNT(DISTINCT ee.envios_encaminamiento_id) as movimientos'),
                DB::raw('COUNT(DISTINCT r.registro_entrega_id) as entregas'),
                DB::raw('MIN(r.created_at) as primera_entrega')
            )
            ->orderBy('e.envio_id')
            ->get();
    }

    /**
     * Aplica la regla de negocio para elegir qué envío conserva el código original.
     * Devuelve la fila ganadora con el motivo, para que quede en el log.
     */
    private function elegirQueConserva($filas)
    {
        $entregados = $filas->where('entregas', '>', 0);

        if ($entregados->count() === 1) {
            $ganador = $entregados->first();
            $ganador->motivo = 'único entregado';
            return $ganador;
        }

        if ($entregados->count() > 1) {
            // Varios clientes recibieron el mismo código: gana quien lo recibió primero.
            $ganador = $entregados->sortBy([
                ['primera_entrega', 'asc'],
                ['envio_id', 'asc'],
            ])->first();
            $ganador->motivo = 'entrega más antigua';
            return $ganador;
        }

        // Ninguno entregado: gana el que más avanzó en el flujo; a igualdad, el más antiguo.
        $ganador = $filas->sortBy([
            ['movimientos', 'desc'],
            ['created_at', 'asc'],
            ['envio_id', 'asc'],
        ])->first();
        $ganador->motivo = 'más movimientos';

        return $ganador;
    }

    /** Primer sufijo libre, por si el código con sufijo ya existiera. */
    private function codigoLibre(string $base, int $sufijo): string
    {
        do {
            $candidato = "{$base}-D{$sufijo}";
            $ocupado = DB::table('envios')->whereRaw('UPPER(codigo_envio) = ?', [$candidato])->exists();
            $sufijo++;
        } while ($ocupado);

        return $candidato;
    }

    /** El código vive en tres tablas; las tres se actualizan juntas. */
    private function aplicarCodigo(int $envioId, string $codigo): void
    {
        DB::transaction(function () use ($envioId, $codigo) {
            $anterior = DB::table('envios')->where('envio_id', $envioId)->value('codigo_envio');

            DB::table('envios')->where('envio_id', $envioId)->update(['codigo_envio' => $codigo]);

            DB::table('envios_almacen')->where('envio_id', $envioId)->update(['codigo' => $codigo]);

            DB::table('registros_entregas')->where('envio_id', $envioId)->update(['codigo_envio' => $codigo]);

            \Log::info('[CORRECCION CODIGO DUPLICADO] envio_id=' . $envioId . ' ' . $anterior . ' -> ' . $codigo);
        });
    }
}