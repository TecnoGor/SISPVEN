<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;



class GastoOperativoExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize, WithCustomStartCell
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $gastos;

    public function __construct($gastos)
    {
        $this->gastos = $gastos;
    }
    public function collection()
    {
        return $this->gastos;
    }
    public function headings(): array
    {
        return [
            /*'GASTO OPERATIVO',
            'MONTO',
            'FECHA DE CREACIÓN',*/];
    }
    public function map($gasto): array
    {
        return [
            // GASTO OPERATIVO (A6:C6)
            $gasto->tipo_gasto ? $gasto->tipo_gasto->tipo_gasto_operativo : 'No disponible',
            '',
            '',
            // MONTO (D6:F6)
            $gasto->monto ? number_format($gasto->monto, 2) : '0.00',
            '',
            '',
            // FECHA DE CREACIÓN (G6:I6)
            $gasto->created_at ? $gasto->created_at->format('d-m-Y') : 'No disponible',
            '',
            '',
        ];
    }
    public function startCell(): string
    {
        return 'A7'; // Ajusta según dónde quieres los datos
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
                /*$drawing->setHeight(10); // Puedes ajustar la altura*/
                $drawing->setWidth(700); // Ajusta el ancho para que llegue hasta la columna L (prueba valores entre 800 y 1000)
                $drawing->setCoordinates('A1'); // Posición donde se colocará
                $drawing->setWorksheet($sheet);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(10);
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
                $sheet->mergeCells("A4:I4");
                $sheet->setCellValue('A4', 'REPORTE DE GASTOS OPERATIVOS');
                $sheet->getStyle("A4:I4")->applyFromArray($encabezado_azul);

                $sheet->mergeCells("A5:C6");
                $sheet->setCellValue('A5', 'GASTO OEPERATIVO');
                $sheet->getStyle("A5:C6")->applyFromArray($encabezado_azul);

                $sheet->mergeCells("D5:F6");
                $sheet->setCellValue('D5', 'MONTO');
                $sheet->getStyle("D5:F6")->applyFromArray($encabezado_azul);

                $sheet->mergeCells("G5:I6");
                $sheet->setCellValue('G5', 'FECHA DE CREACIÓN');
                $sheet->getStyle("G5:I6")->applyFromArray($encabezado_azul);

                // Fusionar celdas de datos y centrar
                $highestRow = $sheet->getHighestRow();
                for ($row = 6; $row <= $highestRow; $row++) {
                    // Fusionar rangos para cada columna de datos
                    $sheet->mergeCells("A{$row}:C{$row}");
                    $sheet->mergeCells("D{$row}:F{$row}");
                    $sheet->mergeCells("G{$row}:I{$row}");

                    // Centrar el texto en los rangos fusionados
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center',
                        ],
                    ]);
                }

                /*$simpleTitles = [
                    'A' => 'GASTO OPERATIVO',
                    'B' => 'MONTO',
                    'C' => 'FECHA DE CREACIÓN',
                ];

                foreach ($simpleTitles as $col => $title) {
                    $sheet->mergeCells("{$col}5:{$col}6");
                    $sheet->setCellValue("{$col}5", $title);
                    $sheet->getStyle("{$col}5:{$col}6")->applyFromArray($encabezado_azul);
                }*/

                // Bordes para todo el bloque
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:I{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF333333'],
                        ],
                    ],
                ]);

                // Autoajuste de ancho
                foreach (range('A', 'I') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Zebra
                for ($row = 6; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF7F7F7'],
                            ],
                        ]);
                    }
                }


                // Centrar datos desde la fila 6 hasta la última
                for ($row = 6; $row <= $highestRow; $row++) {
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
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
