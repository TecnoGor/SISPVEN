<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ControlDeGastosExport implements WithEvents, WithTitle
{
    protected $oficinas;

    public function __construct($oficinas)
    {
        $this->oficinas = $oficinas;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // --- ENCABEZADO INSTITUCIONAL ---
                $sheet->mergeCells('A1:W1');
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

                // Autosize columnas desde A hasta H
                foreach (range('A', 'C') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Estilo común para encabezados azules
                $encabezado_azul = [
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 14
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
                // TITULO
                $sheet->mergeCells('A4:AM5');
                $sheet->setCellValue('A4', 'CONTROL DE GASTOS DE LAS OFICINA POSTALES TELEGRÁFICAS');
                $sheet->getStyle('A4:AM5')->applyFromArray($encabezado_azul);
                $sheet->getRowDimension(4)->setRowHeight(12);
                $sheet->getRowDimension(5)->setRowHeight(12);
                $sheet->getRowDimension(6)->setRowHeight(15);
                
                

                // Opcional: aplicar bordes a los títulos
                $sheet->getStyle('A4:AM4')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                $sheet->getStyle('A7:AM8')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // --- FILA 5: AGRUPADORES / ENCABEZADOS ---
                // Agrupaciones especiales
                $sheet->mergeCells('A7:A8');
                $sheet->setCellValue('A7', 'N°');

                $sheet->mergeCells('B7:B8');
                $sheet->setCellValue('B7', 'ESTADO');

                $sheet->mergeCells('C7:C8');
                $sheet->setCellValue('C7', 'OPT');

                $sheet->mergeCells('D7:F7');
                $sheet->setCellValue('D7', 'ESTATUS DE LA OPT');

                $sheet->mergeCells('G7:I7');
                $sheet->setCellValue('G7', 'ARRENDAMIENTO LOCAL');

                $sheet->mergeCells('J7:L7');
                $sheet->setCellValue('J7', 'CONDOMINIO');

                $sheet->mergeCells('M7:O7');
                $sheet->setCellValue('M7', 'SERVICIO ELECTRICO');

                $sheet->mergeCells('P7:R7');
                $sheet->setCellValue('P7', 'SERVICIO DE AGUA');

                $sheet->mergeCells('S7:U7');
                $sheet->setCellValue('S7', 'SERVICIO DE INTERNET');

                $sheet->mergeCells('V7:X7');
                $sheet->setCellValue('V7', 'SERVICIO DE ASEO');

                $sheet->mergeCells('AK7:AM8');
                $sheet->setCellValue('AK7', 'OBSERVACIONES');

                $otros_gastos = [
                    'Y' => 'AB',
                    'AC' => 'AF',
                    'AG' => 'AJ'
                ];

                foreach($otros_gastos as $colum1 => $colum2){
                    $sheet->mergeCells("{$colum1}7:{$colum2}7");
                    $sheet->setCellValue("{$colum1}7", 'OTROS GASTOS');
                }

                $sheet->setCellValue('D8', 'PROPIA');
                $sheet->setCellValue('E8', 'ARRENDADA');
                $sheet->setCellValue('F8', 'COMODATO');
                $sheet->setCellValue('G8', 'CANON (ACTUAL)');
                $sheet->setCellValue('H8', 'MESES PENDIENTES');
                $sheet->setCellValue('I8', 'TOTAL DEUDA');
                $sheet->setCellValue('J8', 'MONTO ESTABLECIDO');
                $sheet->setCellValue('K8', 'MESES PENDIENTES');
                $sheet->setCellValue('L8', 'TOTAL DEUDA');

                $servicios_col = [
                    'M8', 'N8', 'O8', 'P8', 'Q8', 'R8', 'S8', 'T8', 'U8', 'V8', 'W8', 'X8'
                ];

                $servicio = [
                    'MONTO', 'MESES PENDIENTES', 'TOTAL DEUDA'
                ];

                foreach ($servicios_col as $i => $col){
                    $titulo = $servicio[$i %count($servicio)];
                    $columna = $col;
                    $tit = $titulo;
                    
                    $sheet->setCellValue($columna, "$tit");
                }

                $servicios_col2 = [
                    'Y8', 'Z8', 'AA8', 'AB8', 'AC8', 'AD8', 'AE8', 'AF8', 'AG8', 'AH8', 'AI8', 'AJ8'
                ];

                $servicio2 = [
                    'INDIQUE EL GASTO', 'MONTO', 'MESES PENDIENTES', 'TOTAL DEUDA'
                ];

                foreach ($servicios_col2 as $i => $col){
                    $titulo = $servicio2[$i %count($servicio2)];
                    $columna = $col;
                    $tit = $titulo;
                    
                    $sheet->setCellValue($columna, "$tit");
                }

                foreach (range('D', 'X') as $letra) {
                    $sheet->getColumnDimension($letra)->setAutoSize(true);
                }

                foreach ($servicios_col2 as $col) {
                    $letra = preg_replace('/\d/', '', $col);
                    $sheet->getColumnDimension($letra)->setAutoSize(true);
                }
                

                //Cuando los titulos de las columnas se colocan con un foreach usar el setAutoSize dentro del mismo foreach para que funcione. 
                //Usar solo la letra de la columna para el getColumnDimension.

                // Estilos para todo el rango de encabezados
                $sheet->getStyle('A7:AM7')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Estilos para todo el rango de subencabezados
                $sheet->getStyle('D8:AJ8')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '808080'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                //Datos de las oficinas
                $row = 9;
                $contador = 1;
                foreach ($this->oficinas as $oficina) {
                    // Agrupar deudas por servicio
                    $gastos = $oficina->pagos_servicios_publicos
                        ->groupBy('servicio_publico_id');

                    $electricidad = $gastos[1] ?? collect();
                    $agua = $gastos[2] ?? collect();
                    $aseo = $gastos[3] ?? collect();
                    $internet = $gastos[4] ?? collect();

                    $sheet->setCellValue("A{$row}", $contador);
                    $sheet->setCellValue("B{$row}", $oficina->estado->nombre);
                    $sheet->setCellValue("C{$row}", $oficina->nombre);
                    $condicion = $oficina->semaforo_postal->condicion ?? null;
                    $sheet->setCellValue("D{$row}", $condicion === 'Propia Ipostel' ? 'X' : null);
                    $sheet->setCellValue("E{$row}", $condicion === 'Arrendada'      ? 'X' : null);
                    $sheet->setCellValue("F{$row}", $condicion === 'En Comodato'    ? 'X' : null);
                    $arrendamiento = $oficina->gasto_arrendamiento;
                    $sheet->setCellValue("H{$row}", $arrendamiento->count() ?: null);
                    $sheet->setCellValue("I{$row}", $arrendamiento->count() ? $arrendamiento->sum('monto') . ' Bs' : null);
                    $sheet->setCellValue("N{$row}", $electricidad->count());
                    $sheet->setCellValue("O{$row}", $electricidad->sum('monto').' '.'Bs');
                    $sheet->setCellValue("Q{$row}", $agua->count());
                    $sheet->setCellValue("R{$row}", $agua->sum('monto').' '.'Bs');
                    $sheet->setCellValue("T{$row}", $internet->count());
                    $sheet->setCellValue("U{$row}", $internet->sum('monto').' '.'Bs');
                    $sheet->setCellValue("W{$row}", $aseo->count());
                    $sheet->setCellValue("X{$row}", $aseo->sum('monto').' '.'Bs');

                    $row++;
                    $contador++; 
                }

                // Centrar toda la información de datos
                if ($row > 9) {
                    $sheet->getStyle("A9:AM" . ($row - 1))->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }
            },
        ];
    }

    public function title(): string
    {
        return 'control_de_gastos';
    }
}
