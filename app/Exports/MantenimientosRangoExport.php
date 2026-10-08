<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MantenimientosRangoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $mantenimientos;
    protected $fechaDesde;
    protected $fechaHasta;
    protected $oficinaNombre;

    public function __construct($mantenimientos, $fechaDesde, $fechaHasta, $oficinaNombre = 'Todas las oficinas')
    {
        $this->mantenimientos = $mantenimientos;
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
        $this->oficinaNombre = $oficinaNombre;
    }

    public function collection()
    {
        return $this->mantenimientos;
    }

    public function map($mantenimiento): array
    {
        return [
            $mantenimiento->descripcion,
            $mantenimiento->vehiculo->oficina->nombre ?? 'N/A',
            $mantenimiento->vehiculo->placa ?? 'N/A',
            $mantenimiento->vehiculo->modelo ?? 'N/A',
            \Carbon\Carbon::parse($mantenimiento->created_at)->format('d/m/Y h:i A'),
            $mantenimiento->detalles->pluck('servicioFlota.nombre')->implode(', ')
        ];
    }

    public function headings(): array
    {
        return [
            ["Reporte de mantenimientos de flota de la oficina: {$this->oficinaNombre}"],
            ["Desde: {$this->fechaDesde} - Hasta: {$this->fechaHasta}"],
            [], // Línea vacía para separar
            ['Descripción', 'Agencia', 'Placa', 'Modelo', 'Fecha de Registro', 'Servicios Realizados'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Dar formato de negrita al título y subtítulo
        $sheet->mergeCells('A1:F1'); // Unir celdas para el título
        $sheet->mergeCells('A2:F2'); // Unir celdas para el rango de fechas
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);

        // Encabezados de columna
        $sheet->getStyle('A4:F4')->getFont()->setBold(true);

        // Ajustar el ancho de columnas
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Reporte Mantenimientos';
    }
}
