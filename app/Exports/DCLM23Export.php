<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;




class DCLM23Export implements WithEvents, WithTitle
{


    protected $envios;
    protected $estado;
    protected $oficina;
    protected $regiones;
    protected $mesReporte;

    public function __construct($envios, $estado, $oficina, $regiones, $mesReporte)
    {
        $this->envios = $envios;
        $this->estado = $estado;
        $this->oficina = $oficina;
        $this->regiones = $regiones;
        $this->mesReporte = $mesReporte;
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
                // Estado / Región
                $sheet->mergeCells('A4:B4');
                $sheet->setCellValue('A4', 'ESTADO / REGIÓN:');
                $sheet->getStyle('A4:B4')->applyFromArray($encabezado_azul);

                // Valor de región y estado juntos
                $regionEstado = trim(($this->estado ? $this->estado : '') . ' - ' . ($this->regiones ? $this->regiones : '') );
                $sheet->mergeCells('C4:C4');
                $sheet->setCellValue('C4', $regionEstado);
                $sheet->getStyle('C4')->applyFromArray([
                    'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Oficina
                $sheet->mergeCells('D4:E4');
                $sheet->setCellValue('D4', 'OFICINA POSTAL TELEGRÁFICA:');
                $sheet->getStyle('D4:E4')->applyFromArray($encabezado_azul);

                // Valor de la oficina (justo al lado)
                $sheet->mergeCells('F4:F4');
                $sheet->setCellValue('F4', $this->oficina->nombre ?? '');
                $sheet->getStyle('F4')->applyFromArray([
                    'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Mes de Reporte
                $sheet->mergeCells('G4:H4');
                $sheet->setCellValue('G4', 'MES DE REPORTE:');
                $sheet->getStyle('G4:H4')->applyFromArray($encabezado_azul);

                // Valor del mes de reporte
                $sheet->mergeCells('I4:I4');
                $sheet->setCellValue('I4', $this->mesReporte ?? '');
                $sheet->getStyle('I4')->applyFromArray([
                    'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Opcional: aplicar bordes a los títulos
                $sheet->getStyle('A4:H4')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Aplicar bordes también a las celdas de respuesta (C4, F4, I4)
                foreach (['C4', 'F4', 'I4'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                    ]);
                }

                // --- FILA 5: AGRUPADORES / ENCABEZADOS ---
                // Agrupaciones especiales
                $sheet->mergeCells('K6:L6'); // DETALLE DEL ENVÍO
                $sheet->setCellValue('K6', 'DETALLE DEL ENVÍO');

                $sheet->mergeCells('U6:V6'); // TIPO DE PAGO
                $sheet->setCellValue('U6', 'TIPO DE PAGO');

                // IVA agrupado
                $sheet->mergeCells('S6:S6'); // solo una celda en fila 6
                $sheet->setCellValue('S6', 'IVA');

                // Subencabezados de agrupaciones en fila 7
                $sheet->setCellValue('K7', 'PESO (gr)');
                $sheet->setCellValue('L7', 'VOLUMEN');
                $sheet->setCellValue('S7', '16 %');
                $sheet->setCellValue('U7', 'INDIQUE SOPORTE BANCARIO / DEPÓSITO');
                $sheet->setCellValue('V7', 'INDIQUE EL NÚMERO BANCARIO / DEPÓSITO');

                // Títulos simples: columnas que deben ocupar filas 6-7 fusionadas verticalmente
                $simpleTitles = [
                    'A' => 'N°',
                    'B' => 'FECHA CONSIGNACIÓN',
                    'C' => 'CÓDIGO FACTURA DE CONTADO',
                    'D' => 'CÓDIGO DEL ENVÍO',
                    'E' => 'OFICINA DE ORIGEN',
                    'F' => 'OFICINA DE DESTINO',
                    'G' => 'ESTADO DE DESTINO',
                    'H' => 'NOMBRE DEL USUARIO REMITENTE',
                    'I' => 'CÉDULA DE IDENTIDAD',
                    'J' => 'NOMBRE DE USUARIO DESTINATARIO',
                    'M' => 'INDIQUE TIPO DE SERVICIO',
                    'N' => 'COBERTURA',
                    'O' => 'STATUS DEL ENVÍO',
                    'P' => 'ENVIADO POR LA VÍA',
                    'Q' => 'EXENTO DE IVA',
                    'R' => 'SUB-TOTAL POR EL SERVICIO',
                    'T' => 'TOTAL A PAGAR',
                    'W' => 'OBSERVACIONES'
                ];

                foreach ($simpleTitles as $col => $title) {
                    $sheet->mergeCells("{$col}6:{$col}7");
                    $sheet->setCellValue("{$col}6", $title);
                }

                // Estilos para todo el rango de encabezados
                $sheet->getStyle('A6:W7')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Ajuste de ancho automático
                foreach (range('A', 'W') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Altura de filas
                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(6)->setRowHeight(25);

                // Filtramos antes de recorrer
                $enviosFiltrados = $this->envios->filter(function ($envio) {
                    return strtolower($envio->servicio?->nombre) !== 'TELEGRAMA';
                });

                // --- Ahora recorrer los datos ---
                $row = 8; // Inicia después de tus títulos
                $contador = 1;
                $totalRow = 0;
                $sumSub   = 0.0;
                $sumIva   = 0.0;
                $sumTotal = 0.0;
                $sumPeso  = 0.0;
                $sumVol   = 0.0;
                foreach ($this->envios as $envio) {
                    $iva = $envio->coste_sin_iva;
                    $total = $envio->coste + $iva;

                    $sheet->setCellValue("A{$row}", $contador); // N°
                    $sheet->setCellValue("B{$row}", $envio->created_at->format('d/m/Y')); // Fecha
                    $sheet->setCellValue("C{$row}", 'N/A'); // Código factura contado (vacío)
                    $sheet->setCellValue("D{$row}", $envio->codigo_envio); // Código del envío
                    $sheet->setCellValue("E{$row}", $envio->oficinaOrigen?->nombre ?? 'N/A');         // Oficina origen
                    $sheet->setCellValue("F{$row}", $envio->oficinaDestino?->nombre ?? 'N/A');   // Oficina destino
                    $sheet->setCellValue("G{$row}", $envio->estadoDestino?->nombre ?? 'N/A'); // Estado destino
                    $sheet->setCellValue("H{$row}", $envio->nombre_rem . ' ' . $envio->apellido_rem); // Nombre remitente
                    $sheet->setCellValue("I{$row}", $envio->documento_rem); // Cédula remitente
                    $sheet->setCellValue("J{$row}", $envio->nombre_dest . ' ' . $envio->apellido_dest); // Nombre destinatario
                    // Escribir peso (ejemplo en columna O)
                    $sheet->setCellValueExplicit("K{$row}", (float)$envio->peso, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC); // Peso con unidad

                    // Escribir volumen (ejemplo en columna P, si ahí lo tienes)
                    $sheet->setCellValueExplicit("L{$row}", (float)$envio->volumen, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

                    $sheet->setCellValue("M{$row}", $envio->servicio?->nombre ?? '');       // Tipo de servicio
                    $sheet->setCellValue("N{$row}", 'N/A'); // Cobertura (vacío)
                    $sheet->setCellValue("O{$row}", $envio->envio_encaminamientos->last()?->envio_estatus?->estatus ?? 'N/A'); // Status del envío
                    $sheet->setCellValue("P{$row}", 'N/A'); // Enviado por la vía
                    $sheet->setCellValue("Q{$row}", 'No'); // Exento de IVA (asumo que no)
                    $sheet->setCellValueExplicit("R{$row}", (float) $envio->coste, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("S{$row}", (float) $iva,    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("T{$row}", (float) $total,  \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->getStyle("R{$row}:T{$row}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00 "Bs"');
                    // acumular
                    $sumSub   += (float)$envio->coste;
                    $sumIva   += $iva;
                    $sumTotal += $total;
                    $sumPeso  += (float)$envio->peso;
                    $sumVol   += (float)$envio->volumen;
                    $sheet->setCellValue("U{$row}", 'N/A'); // Soporte bancario (vacío)
                    $sheet->setCellValue("V{$row}", 'N/A'); // Número bancario (vacío)
                    $sheet->setCellValue("W{$row}", 'N/A'); // Observaciones (vacío)

                    $row++;
                    $contador++;

                    $lastRow = $row - 1; // porque $row ya se incrementó en el loop
                    $sheet->getStyle("A8:W{$lastRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical'   => 'center',
                        ],
                    ]);
                }
                $firstDataRow = 8;
                $lastRow      = $row - 1;


                if ($lastRow >= $firstDataRow) {
                    $totalRow = $row;

                    // Unificar etiqueta TOTALES de A a Q y ponerla a la derecha
                    $sheet->mergeCells("A{$totalRow}:J{$totalRow}");
                    $sheet->setCellValue("A{$totalRow}", 'TOTALES');

                    // Estilo azul en toda la fila de totales
                    $sheet->getStyle("A{$totalRow}:L{$totalRow}")->applyFromArray($encabezado_azul);
                    $sheet->getStyle("R{$totalRow}:T{$totalRow}")->applyFromArray($encabezado_azul);

                    // Re-alinear SOLO la etiqueta (A:Q) a la derecha (el encabezado la centra, así que reescribimos)
                    $sheet->getStyle("A{$totalRow}:J{$totalRow}")
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    // Totales de peso y volumen
                    $sheet->setCellValueExplicit("K{$totalRow}", round($sumPeso, 2), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("L{$totalRow}", round($sumVol, 2),  \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

                    // Totales (VALORES, no fórmulas)
                    $sheet->setCellValueExplicit("R{$totalRow}", round($sumSub, 2),   \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("S{$totalRow}", round($sumIva, 2),   \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("T{$totalRow}", round($sumTotal, 2), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

                    // Formato monetario para totales
                    $sheet->getStyle("R{$totalRow}:T{$totalRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00 "Bs"');

                    // Bordes/alineación del bloque de datos + totales (ojo: 'vertical' correcto)
                    $sheet->getStyle("A{$firstDataRow}:J{$totalRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders'   => [
                            'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                        ],
                    ]);
                }
            },
        ];
    }

    public function title(): string
    {
        return 'DCLM23';
    }
}
