<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class InventarioGeneralExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $inventarios;

    public function __construct($inventarios)
    {
        $this->inventarios = $inventarios;
    }

    public function collection()
    {
        return $this->inventarios;
    }

    public function headings(): array
    {
        return [
            'INSUMO',
            'CANTIDAD DISPONIBLE',
            'ÚLTIMA ACTUALIZACIÓN',
        ];
    }
    public function map($inventario): array
    {
        // Si tienes relación insumos, puedes mostrar la descripción
        $insumo = $inventario->insumos->first();
        return [
            $insumo ? $insumo->descripcion : 'No disponible',
            $inventario->cantidad ?: 'Sin existencias',
            $inventario->updated_at ? $inventario->updated_at->format('d-m-Y') : 'No disponible',
        ];
    }

    public function startCell(): string
    {
        return 'A6'; // Headings en fila 4-5, datos desde la fila 6
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $colCount = count($this->headings());
                $lastColumn = Coordinate::stringFromColumnIndex($colCount);

                $drawing = new Drawing();
                $drawing->setName('cintillo');
                $drawing->setDescription('Encabezado institucional');
                $drawing->setPath(public_path('images/cintillo.jpg')); // Ruta de tu imagen
                $drawing->setHeight(60); // Ajusta según necesites
                $drawing->setCoordinates('A1'); // Posición donde se colocará
                $drawing->setOffsetX(10); // Opcional: espacio desde el borde izquierdo
                $drawing->setOffsetY(5);  // Opcional: espacio desde el borde superior
                $drawing->setWorksheet($sheet);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle("A1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['argb' => 'FF222222']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
                

                // Encabezados fusionados en filas 4 y 5
                $encabezado_azul = [
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin'],
                    ],
                ];
                $sheet->mergeCells("A4:{$lastColumn}4");
                $sheet->setCellValue('A4', 'REPORTE DE INVENTARIO GENERAL DE INSUMOS');
                $sheet->getStyle("A4:C4")->applyFromArray($encabezado_azul);

                $simpleTitles = [
                    'A' => 'INSUMO',
                    'B' => 'CANTIDAD DISPONIBLE',
                    'C' => 'ÚLTIMA ACTUALIZACIÓN',
                ];

                foreach ($simpleTitles as $col => $title) {
                    $sheet->mergeCells("{$col}5:{$col}6");
                    $sheet->setCellValue("{$col}5", $title);
                    $sheet->getStyle("{$col}5:{$col}6")->applyFromArray($encabezado_azul);
                }

                // Bordes para todo el bloque
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:C{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF333333'],
                        ],
                    ],
                ]);
                // Autoajuste de ancho
                foreach (range('A', 'C') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Zebra
                for ($row = 6; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF7F7F7'],
                            ],
                        ]);
                    }
                }


                // Centrar datos desde la fila 6 hasta la última
                for ($row = 6; $row <= $highestRow; $row++) {
                    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center',
                        ],
                    ]);
                }
            },
        ];
    }
}
