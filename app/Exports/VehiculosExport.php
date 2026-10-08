<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VehiculosExport implements FromView
{
    protected $vehiculos;
    protected $oficinaNombre;

    public function __construct($vehiculos, $oficinaNombre = 'Sin Oficina')
    {
        $this->vehiculos = $vehiculos;
        $this->oficinaNombre = $oficinaNombre;
    }

    public function view(): View
    {
        return view('exports.vehiculos', [
            'vehiculos' => $this->vehiculos,
            'oficinaNombre' => $this->oficinaNombre
        ]);
    }
}
