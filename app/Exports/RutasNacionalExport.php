<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RutasExport implements FromView
{
    protected $rutas;
    protected $nombreOficinaUsuario;

    public function __construct($rutas, $nombreOficinaUsuario)
    {
        $this->rutas = $rutas;
        $this->nombreOficinaUsuario = $nombreOficinaUsuario;
    }

    public function view(): View
    {
        return view('exports.reporte-rutas-nacional', [
            'rutas' => $this->rutas,
            'nombreOficinaUsuario' => $this->nombreOficinaUsuario,
        ]);
    }
}
