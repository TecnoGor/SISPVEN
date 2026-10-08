<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class ClientesCorporativosExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $clientes;

    public function __construct($clientes)
    {
        $this->clientes = $clientes;
    }

    public function collection()
    {
        return $this->clientes->map(function ($cliente) {
            return [
                $cliente->razon_social ?? 'No encontrado',
                $cliente->tipo_documento. ' ' .$cliente->numero_documento ?? 'Sin documento',
                $cliente->agente_autorizado ?? 'Sin agente autorizado',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Razón Social',
            'Documento',
            'Agente Autorizado',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => 'center'],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF800000'], // Fondo oscuro
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Combinar celdas para el título
                $sheet->mergeCells('A1:C1');
                $sheet->setCellValue('A1', 'Reporte de Clientes Corporativos');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Ajustar automáticamente el ancho de las columnas
                foreach (range('A', 'C') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Bordes para las filas con datos
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A2:C{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}


