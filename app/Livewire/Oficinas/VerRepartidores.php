<?php

namespace App\Livewire\Oficinas;

use Livewire\Component;
use App\Models\EnvioAlmacen;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class VerRepartidores extends Component
{
    public $open_modal = false;
    public $repartidor_seleccionado = '';
    public $envios_lista = [];
    public $usuario;

    protected $nombres_estatus = [
        16 => 'Asignado a Repartidor',
        18 => 'Recibido por Repartidor',
    ];

    public function mount()
    {
        $this->usuario = auth()->user();
    }

    public function ver_detalles($id_cartero, $nombre)
    {
        $this->repartidor_seleccionado = $nombre;
        
        $ids_envios_asignados = DB::table('asignacion_envio_cartero')
            ->where('user_id', $id_cartero)
            ->pluck('envio_almacen_id');

        $envios = EnvioAlmacen::whereIn('envio_almacen_id', $ids_envios_asignados)
            ->where('envios_almacen.oficina_id', $this->usuario->oficina_id) 
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('envios_encaminamiento as ee')
                    ->whereColumn('ee.envio_id', 'envios_almacen.envio_id')
                    ->whereIn('ee.estatus_id', [16, 18])
                    ->whereRaw('ee.envios_encaminamiento_id = (SELECT MAX(ee2.envios_encaminamiento_id) FROM envios_encaminamiento ee2 WHERE ee2.envio_id = envios_almacen.envio_id)');
            })
            ->select('envios_almacen.codigo', 'envios_almacen.envio_id')
            ->get();

        $this->envios_lista = $envios->map(function ($item) {
            $ultimo_estatus = DB::table('envios_encaminamiento')
                ->where('envio_id', $item->envio_id)
                ->orderBy('envios_encaminamiento_id', 'desc')
                ->first();

            return [
                'tracking'   => $item->codigo ?? 'SIN-CODIGO',
                'id_estatus' => $ultimo_estatus->estatus_id, 
            ];
        })->toArray();

        $this->open_modal = !empty($this->envios_lista);
    }

    public function obtenerTextoEstatus($id)
    {
        return $this->nombres_estatus[$id] ?? 'Estatus Desconocido';
    }

    public function render()
    {
        $carteros_con_conteo = User::whereHas('roles', function ($query) {
                $query->where('id', 7);
            })
            ->where('oficina_id', $this->usuario->oficina_id)
            ->where('activo', true)
            ->addSelect(['conteo_real' => DB::table('asignacion_envio_cartero')
                ->join('envios_almacen', 'asignacion_envio_cartero.envio_almacen_id', '=', 'envios_almacen.envio_almacen_id')
                ->selectRaw('count(*)')
                ->whereColumn('asignacion_envio_cartero.user_id', 'users.id')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('envios_encaminamiento as ee')
                        ->whereColumn('ee.envio_id', 'envios_almacen.envio_id')
                        ->whereIn('ee.estatus_id', [16, 18])
                        ->whereRaw('ee.envios_encaminamiento_id = (SELECT MAX(ee2.envios_encaminamiento_id) FROM envios_encaminamiento ee2 WHERE ee2.envio_id = envios_almacen.envio_id)');
                })
            ])
            ->get();

        return view('livewire.oficinas.ver-repartidores', [
            'carteros' => $carteros_con_conteo, 
        ]);
    }
}