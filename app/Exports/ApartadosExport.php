<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithTitle;


class ApartadosExport implements ShouldAutoSize, WithEvents
{
    public $apartados;

    public function __construct($apartados)
    {
        $this->apartados = $apartados;
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:F1');
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
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Estilo común para encabezados azules
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

                // SUBENCABEZADOS DE INFORMACIÓN //
                // Código
                $sheet->setCellValue('A4', 'CÓDIGO');
                $sheet->getStyle('A5')->applyFromArray($encabezado_azul);

                // Condición
                $sheet->setCellValue('B4', 'CONDICIÓN');
                $sheet->getStyle('B4')->applyFromArray($encabezado_azul);

                // Documento
                $sheet->setCellValue('C4', 'NRO DOCUMENTO');
                $sheet->getStyle('C4')->applyFromArray($encabezado_azul);

                // Cliente
                $sheet->setCellValue('D4', 'CLIENTE');
                $sheet->getStyle('D4')->applyFromArray($encabezado_azul);

                //Días restantes
                $sheet->setCellValue('E4', 'DÍAS RESTANTES');
                $sheet->getStyle('E4')->applyFromArray($encabezado_azul);

                // Estatus
                $sheet->setCellValue('F4', 'ESTATUS');
                $sheet->getStyle('F4')->applyFromArray($encabezado_azul);
                // Opcional: aplicar bordes a los títulos
                $sheet->getStyle('A4:F4')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                $simpleTitles = [
                    'A' => 'CÓDIGO',
                    'B' => 'CONDICIÓN',
                    'C' => 'NRO DOCUMENTO',
                    'D' => 'CLIENTE',
                    'E' => 'DÍAS RESTANTES',
                    'F' => 'ESTATUS'
                ];

                foreach ($simpleTitles as $col => $title) {
                    $sheet->mergeCells("{$col}4:{$col}5");
                    $sheet->setCellValue("{$col}4", $title);
                    $sheet->getStyle("{$col}4:{$col}5")->applyFromArray($encabezado_azul);
                }

                // Estilos para todo el rango de encabezados
                $sheet->getStyle('A5:F5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Ajustar automáticamente el ancho de las columnas
                foreach (range('A', 'H') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Altura de filas
                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(6)->setRowHeight(25);

                // Datos
                $row = 6; // Inicia en la fila 7, después de los encabezados
                
                foreach ($this->apartados as $apartado) {
                    $registro = $apartado->registro_apartado()->where('activo', true)->latest()->first();
                    $sheet->setCellValue("A{$row}", $apartado->apartado ?: 'No disponible');
                    $sheet->setCellValue("B{$row}", $apartado->operativo ? 'Operativo' : 'Inoperativo');
                    $sheet->setCellValue("C{$row}", $registro ? $registro->tipo_documento . '-' . $registro->documento : 'No existe cliente actual');
                    $sheet->setCellValue("D{$row}", $registro ? $registro->nombre . ' ' . $registro->apellido : 'No existe cliente actual');
                    $sheet->setCellValue("E{$row}", $apartado->dias_faltantes ?? '--');
                    $sheet->setCellValue("F{$row}", $apartado->activo ? 'Activo' : 'Inactivo');
                    $row++;
                }

                // Aplicar bordes a los datos
                $dataRange = "A6:F" . ($row - 1);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin'],
                    ],
                ]);
            },
        ];
    }
}
