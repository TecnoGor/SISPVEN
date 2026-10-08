<?php

namespace App\Exports;

use App\Models\Viaje;
use App\Models\Vehiculo;
use App\Models\Oficina;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class ViajesExport implements FromView
{
    protected $week;
    protected $search;
    protected $user;

    public function __construct($week, $search, $user)
    {
        $this->week = $week;
        $this->search = $search;
        $this->user = $user;
    }

    public function view(): View
    {
        $weekRange = $this->week ? $this->getWeekRange($this->week) : [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ];

        $viajes = Viaje::when($this->user->hasRole('Gerente de Estado'), function ($query) {
                $estadoId = DB::table('usuario_estados')
                    ->where('id_user', $this->user->id)
                    ->value('id_estado');

                $vehiculoIds = Vehiculo::whereHas('oficina', function ($query) use ($estadoId) {
                    $query->where('estado_id', $estadoId)
                          ->where('tipo_oficina_id', 4);
                })->pluck('vehiculo_id');

                $query->whereIn('vehiculo_id', $vehiculoIds);
            })
            ->when($this->user->hasRole(['SuperAdmin', 'Operaciones Nacionales']), function ($query) {
                $query->whereHas('ruta.oficinaDestino', function ($query) {
                    $query->whereIn('tipo_oficina_id', [1, 2, 3]);
                });
            })
            ->when($this->search, function ($query) {
                $query->where('codigo', 'LIKE', '%' . $this->search . '%');
            })
            ->whereBetween('fecha_salida', $weekRange)
            ->get();

        return view('exports.viajes', [
            'viajes' => $viajes
        ]);
    }

    protected function getWeekRange($week)
    {
        $year = substr($week, 0, 4);
        $weekNumber = substr($week, 6);

        $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        return [$startOfWeek, $endOfWeek];
    }
}
