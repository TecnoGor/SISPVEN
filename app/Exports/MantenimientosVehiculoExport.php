<?php
namespace App\Exports;

use App\Models\Vehiculo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MantenimientosVehiculoExport implements FromView
{
    public $vehiculo;
    public $mantenimientos;

    public function __construct($vehiculo, $mantenimientos)
    {
        $this->vehiculo = $vehiculo;
        $this->mantenimientos = $mantenimientos;
    }

    public function view(): View
    {
        return view('exports.mantenimientos-vehiculo', [
            'vehiculo' => $this->vehiculo,
            'mantenimientos' => $this->mantenimientos,
        ]);
    }
}
