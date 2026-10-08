<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InventarioInsumo;
use Illuminate\Support\Facades\DB;

class InventarioDiarioAM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventario-diario-a-m';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Respaldo diario del inventario de insumos a las 6:50 AM';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $inventario = InventarioInsumo::with('insumo')->orderBy('insumo_id')->get();

        $datos = [];

        foreach ($inventario as $item) {
            $datos[] = [
                'oficina_id' => $item->oficina_id,
                'insumo_id' => $item->insumo_id,
                'cantidad' => $item->cantidad,
                'coste' => $item->insumo->costo,
                'fecha' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('respaldo_inventario_diario')->insert($datos);
    }
}
