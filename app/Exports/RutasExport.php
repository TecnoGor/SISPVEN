<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use App\Models\RutasModelo;
use App\Models\Oficina;

class RutasExport implements FromView, WithTitle, WithColumnFormatting, WithHeadings
{
    protected $rutas;
    protected $nombreOficinaUsuario;

    // Recibimos las rutas y el nombre de la oficina del usuario
    public function __construct($rutas, $nombreOficinaUsuario)
    {
        $this->rutas = $rutas;
        $this->nombreOficinaUsuario = $nombreOficinaUsuario;
    }

    // Generamos la vista para el archivo Excel
    public function view(): \Illuminate\Contracts\View\View
    {
        return view('exports.rutas-excel', [
            'rutas' => $this->rutas,
            'nombreOficinaUsuario' => $this->nombreOficinaUsuario,
        ]);
    }

    // Configuramos el título de la hoja de Excel
    public function title(): string
    {
        return 'Rutas';
    }

    // Configuramos las cabeceras del archivo Excel
    public function headings(): array
    {
        return [
            'N°',
            'Ruta',
            'Oficina Origen',
            'Oficina Destino',
            'Estado',
            'Fecha de Creación',
        ];
    }

    // Configuramos el formato de las columnas
    public function columnFormats(): array
    {
        return [
            'A' => '@', // Para la columna A (ID Ruta) como texto
            'B' => '@', // Para la columna B (Ruta) como texto
        ];
    }
}
