<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class HistorialCombustibleExport implements FromCollection, WithHeadings
{
    private $vehiculo;
    private $cargas;

    public function __construct($vehiculo, $cargas)
    {
        $this->vehiculo = $vehiculo;
        $this->cargas = $cargas;
    }

    public function collection()
    {
        return collect($this->cargas)->map(function($carga) {
            return [
                'fecha' => \Carbon\Carbon::parse($carga['fecha'])->format('d/m/Y'),
                'litros' => $carga['litros'],
                'costo_total' => $carga['costo_total'],
                'kilometraje' => $carga['kilometraje'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['Reporte de Consumo de Combustible del Vehículo'],
            ['Marca:', $this->vehiculo->marca, 'Modelo:', $this->vehiculo->modelo, 'Placa:', $this->vehiculo->placa],
            [''], // Fila vacía para separar
            ['Fecha', 'Litros', 'Costo', 'Kilometraje'],
        ];
    }
} 