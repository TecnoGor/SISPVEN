<?php

namespace App\Console\Commands;

use App\Models\InsumoUsuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InventarioUsuarioDiario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventario-usuario-diario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Respaldo diario del inventario de promotores';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inventario = InsumoUsuario::with('insumo')->orderBy('insumo_id')->get();

        $datos = [];

        foreach ($inventario as $item) {
            $datos[] = [
                'oficina_id' => $item->oficina_id,
                'usuario_id' => $item->usuario_id,
                'insumo_id' => $item->insumo_id,
                'cantidad' => $item->cantidad,
                'coste' => $item->insumo->costo,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('respaldo_inventario_usuario')->insert($datos);
    }
}
