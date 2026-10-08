<?php

namespace App\Exports;

use App\Models\OficinaSemaforoPostal;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class SemaforoPostalExport implements WithEvents, WithTitle
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
                //alto de las filas
                $sheet->getRowDimension(1)->setRowHeight(25);
                //ancho de las columnas
                $sheet->getColumnDimension('A')->setWidth(38);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(36);
                $sheet->getColumnDimension('D')->setWidth(38);
                $sheet->getColumnDimension('E')->setWidth(180);
                $sheet->getColumnDimension('F')->setWidth(35);
                $sheet->getColumnDimension('G')->setWidth(35);
                $sheet->getColumnDimension('H')->setWidth(32);
                $sheet->getColumnDimension('I')->setWidth(16);
                $sheet->getColumnDimension('J')->setWidth(16);
                $sheet->getColumnDimension('K')->setWidth(16);
                $sheet->getColumnDimension('L')->setWidth(16);
                $sheet->getColumnDimension('M')->setWidth(16);
                $sheet->getColumnDimension('N')->setWidth(16);
                $sheet->getColumnDimension('O')->setWidth(16);
                $sheet->getColumnDimension('P')->setWidth(16);
                $sheet->getColumnDimension('Q')->setWidth(16);
                $sheet->getColumnDimension('R')->setWidth(16);
                $sheet->getColumnDimension('S')->setWidth(16);
                $sheet->getColumnDimension('T')->setWidth(135);
                $sheet->getColumnDimension('U')->setWidth(46);
                $sheet->getColumnDimension('V')->setWidth(22);
                $sheet->getColumnDimension('W')->setWidth(14);
                $sheet->getColumnDimension('X')->setWidth(31);
                $sheet->getColumnDimension('Y')->setWidth(20);
                $sheet->getColumnDimension('Z')->setWidth(20);
                $sheet->getColumnDimension('AA')->setWidth(20);
                $sheet->getColumnDimension('AB')->setWidth(31);
                $sheet->getColumnDimension('AC')->setWidth(27);
                $sheet->getColumnDimension('AD')->setWidth(15);
                $sheet->getColumnDimension('AE')->setWidth(18);
                $sheet->getColumnDimension('AF')->setWidth(18);
                $sheet->getColumnDimension('AG')->setWidth(47);
                $sheet->getColumnDimension('AH')->setWidth(21);
                $sheet->getColumnDimension('AI')->setWidth(15);
                $sheet->getColumnDimension('AJ')->setWidth(10);
                $sheet->getColumnDimension('AK')->setWidth(10);
                $sheet->getColumnDimension('AL')->setWidth(27);
                $sheet->getColumnDimension('AM')->setWidth(27);
                $sheet->getColumnDimension('AN')->setWidth(121);

                $encabezado_purpura = [
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
                        'startColor' => ['rgb' => '800080'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin'],
                    ],
                ];

                $encabezado_verde = [
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
                        'startColor' => ['rgb' => '006400'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin'],
                    ],
                ];

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

                $encabezado_azul_claro = [
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
                        'startColor' => ['rgb' => '6CA0DC'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin'],
                    ],
                ];

                $encabezado_amarillo = [
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
                        'startColor' => ['rgb' => 'FFDB58'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin'],
                    ],
                ];

                $encabezado_gris = [
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
                        'startColor' => ['rgb' => '808080'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin'],
                    ],
                ];

                // SUBENCABEZADO DE INFORMACIÓN //
                $sheet->mergeCells('A4:AN4');
                $sheet->setCellValue('A4', 'ACTUALIZACIÓN DEL SEMÁFORO OPERATIVO DE LAS OFICINAS POSTALES TELEGRÁFICAS ACTIVAS E INOPERATIVAS');
                $sheet->getRowDimension(4)->setRowHeight(20);

                $sheet->mergeCells('F5:G5');
                $sheet->setCellValue('F5', 'INDICAR CON CANTIDAD (NUMEROS)  SEGÚN LOS INDICADORES');
                $sheet->getStyle('F5:G5')->applyFromArray($encabezado_purpura);

                $sheet->mergeCells('I5:S5');
                $sheet->setCellValue('I5', 'MARCAR CON UNA (X) SEGÚN SUS INDICADORES CORRECTOS');
                $sheet->getStyle('I5:S5')->applyFromArray($encabezado_verde);

                $sheet->mergeCells('T5');
                $sheet->setCellValue('T5', 'RESUMEN DEL INDICADOR');
                $sheet->getStyle('T5')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('U5');
                $sheet->setCellValue('U5', 'INDICAR CON SI O NO SEGÚN LOS INDICADORES');
                $sheet->getStyle('U5')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('V5:AA5');
                $sheet->setCellValue('V5', 'INDICAR CON CANTIDAD (NUMERO) SEGÚN LOS INDICADORES');
                $sheet->getStyle('V5:AA5')->applyFromArray($encabezado_purpura);

                $sheet->mergeCells('AB5:AF5');
                $sheet->setCellValue('AB5', 'MARCAR CON UNA (X) SEGÚN LOS INDICADORES CORRECTOS');
                $sheet->getStyle('AB5:AF5')->applyFromArray($encabezado_amarillo);

                $sheet->mergeCells('AG5');
                $sheet->setCellValue('AG5', 'INDICAR CON CANTIDAD SEGÚN LOS INDICADORES');
                $sheet->getStyle('AG5')->applyFromArray($encabezado_verde);

                $sheet->mergeCells('AH5:AK5');
                $sheet->setCellValue('AH5', 'INDICAR CORRECTAMENTE LOS INDICADORES');
                $sheet->getStyle('AH5:AK5')->applyFromArray($encabezado_azul_claro);

                $sheet->mergeCells('AL5');
                $sheet->setCellValue('AL5', 'MARCAR CON UNA (X)');
                $sheet->getStyle('AL5')->applyFromArray($encabezado_purpura);

                $sheet->mergeCells('AM5');
                $sheet->setCellValue('AM5', 'INDICAR CANTIDAD');
                $sheet->getStyle('AM5')->applyFromArray($encabezado_purpura);

                $sheet->mergeCells('AN5');
                $sheet->setCellValue('AN5', 'INDIQUE RESUMEN DEL INDICADORES');
                $sheet->getStyle('AN5')->applyFromArray($encabezado_purpura);

                //Segunda fila de titulos

                $sheet->mergeCells('A6:A7');
                $sheet->setCellValue('A6', 'REGION');

                $sheet->mergeCells('B6:B7');
                $sheet->setCellValue('B6', 'ENTIDAD');

                $sheet->mergeCells('C6:C7');
                $sheet->setCellValue('C6', 'MUNICIPIO');

                $sheet->mergeCells('D6:D7');
                $sheet->setCellValue('D6', 'PARROQUIA');

                $sheet->mergeCells('E6:E7');
                $sheet->setCellValue('E6', 'DIRECCIÓN EXACTA');

                $sheet->mergeCells('F6:G6');
                $sheet->setCellValue('F6', 'PUNTO Y CÍRCULO');
                $sheet->getStyle('F6')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('H6:H7');
                $sheet->setCellValue('H6', 'OFICINAS POSTALES TELEGRÁFICAS');
                $sheet->getStyle('H6:H7')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('I6:K6');
                $sheet->setCellValue('I6', 'CONDICIÓN');

                $sheet->mergeCells('L6:N6');
                $sheet->setCellValue('L6', 'TIPOLOGÍA');

                $sheet->mergeCells('O6:P6');
                $sheet->setCellValue('O6', 'ESTATUS');

                $sheet->mergeCells('Q6:U6');
                $sheet->setCellValue('Q6', 'INFRAESTRUCTURA');

                $sheet->mergeCells('V6:AA6');
                $sheet->setCellValue('V6', 'RECURSOS HUMANOS');

                $sheet->mergeCells('AB6:AC6');
                $sheet->setCellValue('AB6', 'LEGAL');

                $sheet->mergeCells('AD6:AG6');
                $sheet->setCellValue('AD6', 'TECNOLOGÍA');

                $sheet->mergeCells('AH6:AN6');
                $sheet->setCellValue('AH6', 'VEHÍCULOS');

                // Estilos para todo el rango de encabezados fusionados entre A6 y E7
                $sheet->getStyle('A6:E7')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Estilos para todo el rango de encabezados fusionados entre I6 y AN6
                $sheet->getStyle('I6:AN6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);


                //Tercer fila de titulos

                $sheet->mergeCells('F7');
                $sheet->setCellValue('F7', 'CANTIDAD DEL CONSEJO COMUNAL');
                $sheet->getStyle('F7')->applyFromArray($encabezado_gris);

                $sheet->mergeCells('G7');
                $sheet->setCellValue('G7', 'CANTIDAD DE COMUNAS');
                $sheet->getStyle('G7')->applyFromArray($encabezado_gris);

                $sheet->mergeCells('I7');
                $sheet->setCellValue('I7', 'PROPIA');

                $sheet->mergeCells('J7');
                $sheet->setCellValue('J7', 'ARRENDADA');

                $sheet->mergeCells('K7');
                $sheet->setCellValue('K7', 'COMODATO');

                $sheet->mergeCells('L7');
                $sheet->setCellValue('L7', 'COMERCIAL');

                $sheet->mergeCells('M7');
                $sheet->setCellValue('M7', 'REPARTO');

                $sheet->mergeCells('N7');
                $sheet->setCellValue('N7', 'MIXTA');

                $sheet->mergeCells('O7');
                $sheet->setCellValue('O7', 'ABIERTA');

                $sheet->mergeCells('P7');
                $sheet->setCellValue('P7', 'CERRADA');

                $sheet->mergeCells('Q7');
                $sheet->setCellValue('Q7', 'BUEN ESTADO');

                $sheet->mergeCells('R7');
                $sheet->setCellValue('R7', 'REGULAR');

                $sheet->mergeCells('S7');
                $sheet->setCellValue('S7', 'MAL ESTADO');

                $sheet->mergeCells('T7');
                $sheet->setCellValue('T7', 'INDIQUE CONDICIONES Y REQUERIMIENTO');

                $sheet->mergeCells('U7');
                $sheet->setCellValue('U7', 'INDICAR ESPACIO FISICO SI/NO');

                $sheet->mergeCells('V7');
                $sheet->setCellValue('V7', 'CANTIDAD DE VACANTES');

                $sheet->mergeCells('W7');
                $sheet->setCellValue('W7', 'SIN PERSONAL');

                $sheet->mergeCells('X7');
                $sheet->setCellValue('X7', 'CANTIDAD DE PERSONAL ACTIVOS');

                $sheet->mergeCells('Y7');
                $sheet->setCellValue('Y7', 'JUBILADOS');

                $sheet->mergeCells('Z7');
                $sheet->setCellValue('Z7', 'PENSIONADOS');

                $sheet->mergeCells('AA7');
                $sheet->setCellValue('AA7', 'SOBREVIVIENTES');

                $sheet->mergeCells('AB7');
                $sheet->setCellValue('AB7', 'POR RENOVACION DE CONTRATO');

                $sheet->mergeCells('AC7');
                $sheet->setCellValue('AC7', 'CONTRATO RENOVADO');

                $sheet->mergeCells('AD7');
                $sheet->setCellValue('AD7', 'SIN INTERNET');

                $sheet->mergeCells('AE7');
                $sheet->setCellValue('AE7', 'SIN COMPUTADORA');

                $sheet->mergeCells('AF7');
                $sheet->setCellValue('AF7', 'CON CONECTIVIDAD');

                $sheet->mergeCells('AG7');
                $sheet->setCellValue('AG7', 'CON COMPUTADORA/CANTIDAD');

                $sheet->mergeCells('AH7');
                $sheet->setCellValue('AH7', 'CANTIDAD DE ACTIVO');

                $sheet->mergeCells('AI7');
                $sheet->setCellValue('AI7', 'TIPO');

                $sheet->mergeCells('AJ7');
                $sheet->setCellValue('AJ7', 'MARCA');

                $sheet->mergeCells('AK7');
                $sheet->setCellValue('AK7', 'PLACA');

                $sheet->mergeCells('AL7');
                $sheet->setCellValue('AL7', 'SIN VEHÍCULOS');

                $sheet->mergeCells('AM7');
                $sheet->setCellValue('AM7', 'INOPERATIVOS');

                $sheet->mergeCells('AN7');
                $sheet->setCellValue('AN7', 'INDIQUE CONDICIÓN DE VEHÍCULOS INOPERATIVOS');

                // Estilos para todo el rango de encabezados desde I7 hasta AN7
                $sheet->getStyle('I7:AN7')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '808080'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);


                $fila_inicial = 8;
            foreach ($this->oficinas as $index => $oficina) {
                $fila = $fila_inicial + $index;

                $sheet->setCellValue('A' . $fila, $oficina->estado->region->nombre);
                $sheet->setCellValue('B' . $fila, $oficina->estado->nombre);
                $sheet->setCellValue('C' . $fila, $oficina->municipio->nombre);
                $sheet->setCellValue('D' . $fila, $oficina->parroquia->nombre);
                $sheet->setCellValue('E' . $fila, $oficina->direccion);
                $sheet->setCellValue('F' . $fila, 'N/A');
                $sheet->setCellValue('G' . $fila, 'N/A');

                // CONDICION (I=PROPIA, J=ARRENDADA, K=COMODATO): se marca con X
                // la que corresponda. Si la oficina no tiene registro de semaforo
                // postal, las tres quedan vacias.
                $condicion = $oficina->semaforo_postal?->condicion;

                $sheet->setCellValue('I' . $fila, $condicion === OficinaSemaforoPostal::CONDICION_PROPIA_IPOSTEL ? 'X' : '');
                $sheet->setCellValue('J' . $fila, $condicion === OficinaSemaforoPostal::CONDICION_ARRENDADA ? 'X' : '');
                $sheet->setCellValue('K' . $fila, $condicion === OficinaSemaforoPostal::CONDICION_ENACOMODATO ? 'X' : '');
            }

            // Centrar las X de la seccion CONDICION
            $ultima_fila = $fila_inicial + count($this->oficinas) - 1;
            if ($ultima_fila >= $fila_inicial) {
                $sheet->getStyle('I' . $fila_inicial . ':K' . $ultima_fila)->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
            }

            },
        ];
    }

    public function title(): string
    {
        return 'Semáforo Postal';
    }
}
