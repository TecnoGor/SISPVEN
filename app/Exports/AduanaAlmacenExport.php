<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AduanaAlmacenExport implements FromView
{
    protected $envios;
    protected $titulo;

    public function __construct($envios, $titulo)
    {
        $this->envios = $envios;
        $this->titulo = $titulo;
    }

    public function view(): View
    {
        return view('exports.envios-excel', [
            'envios' => $this->envios,
            'titulo' => $this->titulo,
            'usuario' => auth()->user(),
            'oficina' => auth()->user()->oficina,
        ]);
    }
}
