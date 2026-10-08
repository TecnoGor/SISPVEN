<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SacasExport implements FromView
{
    public $sacas;
    public $pestana;
    public $usuario;
    public $oficina;

    /**
     * @param string $pestana 'abiertas' | 'cerradas' | 'creadas'
     */
    public function __construct($sacas, $pestana, $usuario, $oficina)
    {
        $this->sacas = $sacas;
        $this->pestana = $pestana;
        $this->usuario = $usuario;
        $this->oficina = $oficina;
    }

    public function view(): View
    {
        $titulo = match ($this->pestana) {
            'cerradas' => 'Reporte de Valijas Cerradas',
            'creadas'  => 'Reporte de Valijas Creadas en la Oficina',
            default    => 'Reporte de Valijas Abiertas',
        };

        return view('exports.sacas', [
            'sacas' => $this->sacas,
            'titulo' => $titulo,
            'usuario' => $this->usuario,
            'oficina' => $this->oficina,
        ]);
    }
}