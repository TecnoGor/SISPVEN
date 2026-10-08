<?php

namespace App\Exports;

use App\Models\Viaje;
use App\Models\Vehiculo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ViajesNacionalesExport implements FromView
{
    protected $week;
    protected $search;
    protected $usuario;

    public function __construct($week, $search, $usuario)
    {
        $this->week = $week;
        $this->search = $search;
        $this->usuario = $usuario;
    }

    protected function getWeekRange($week)
    {
        $year = substr($week, 0, 4);
        $weekNumber = substr($week, 6);

        $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        return [$startOfWeek, $endOfWeek];
    }

    public function view(): View
    {
        $weekRange = $this->week ? $this->getWeekRange($this->week) : [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ];

        $viajes = Viaje::when($this->usuario->hasRole('Gerente de Estado'), function ($query) {
                $estadoId = DB::table('usuario_estados')
                    ->where('id_user', $this->usuario->id)
                    ->value('id_estado');

                $vehiculoIds = Vehiculo::whereHas('oficina', function ($query) use ($estadoId) {
                    $query->where('estado_id', $estadoId)
                        ->where('tipo_oficina_id', 4);
                })->pluck('vehiculo_id');

                $query->whereIn('vehiculo_id', $vehiculoIds);
            })
            ->when($this->usuario->hasRole(['SuperAdmin', 'Operaciones Nacionales']), function ($query) {
                $query->whereHas('ruta.oficinaDestino', function ($query) {
                    $query->whereIn('tipo_oficina_id', [1, 2, 3]);
                });
            })
            ->when($this->search, function ($query) {
                $query->where('codigo', 'LIKE', '%' . $this->search . '%');
            })
            ->whereBetween('fecha_salida', $weekRange)
            ->orderBy('ruta_id', 'ASC')
            ->get();

        return view('exports.viajes-nacionales', [
            'viajes' => $viajes
        ]);
    }
}
