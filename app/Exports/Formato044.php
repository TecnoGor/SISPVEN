<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Formato044 implements WithEvents, WithTitle
{

    protected $desde, $hasta, $semana, $oficina, $envios, $insumos, $apartados, $entregas;

    public function __construct($desde, $hasta, $semana, $oficina, $envios, $insumos, $apartados, $entregas)
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->semana = $semana;
        $this->oficina = $oficina;
        $this->envios = $envios;
        $this->insumos = $insumos;
        $this->apartados = $apartados;
        $this->entregas = $entregas;
    }


    public function registerEvents(): array
    {
        return[
            AfterSheet::class => function (AfterSheet $event) {

                $columnas = ['C', 'D', 'E', 'F', 'G'];
                $dias = [
                    'Monday' => 'Lunes',
                    'Tuesday' => 'Martes',
                    'Wednesday' => 'Miércoles',
                    'Thursday' => 'Jueves',
                    'Friday' => 'Viernes',
                ];


                //Totales de envios
                $totales = [];
                foreach ($this->envios as $envio) {
                    $dia = Carbon::parse($envio->created_at)->format('l');

                    if (!array_key_exists($dia, $dias)) {
                        continue;
                    }

                    if (!isset($totales[$dia])) {
                        $totales[$dia] = [
                            'nacional' => 0,
                            'internacional' => 0,
                            'expreso_urbano' => 0,
                            'expreso' => 0,
                            'telegrama' => 0,
                        ];
                    }

                    // Telegramas
                    if ($envio->servicio_id == 3) {
                        $totales[$dia]['telegrama'] += $envio->coste_sin_iva;
                        continue;
                    }

                    // Exprés
                    if ($envio->servicio_id == 9) {
                        if ($envio->servicio_expreso === 'Urbano') {
                            $totales[$dia]['expreso_urbano'] += $envio->coste_sin_iva;
                        } else {
                            $totales[$dia]['expreso'] += $envio->coste_sin_iva;
                        }
                        continue;
                    }

                    // Nacionales e internacionales estándar
                    if ($envio->tipo_envio === 'nacional') {
                        $totales[$dia]['nacional'] += $envio->coste_sin_iva;
                    } elseif ($envio->tipo_envio === 'internacional') {
                        $totales[$dia]['internacional'] += $envio->coste_sin_iva;
                    }
                }


                //totales de insumos
                $totales_insumos = [];
                foreach ($this->insumos as $insumo) {
                $dia = Carbon::parse($insumo->created_at)->format('l');

                if (!array_key_exists($dia, $dias)) {
                    continue;
                }

                if (!isset($totales_insumos[$dia])) {
                    $totales_insumos[$dia] = [
                        'guia_consignacion' => 0,
                        'portaguia' => 0,
                    ];
                }

                if ($insumo->insumo_id == 4) {
                    $totales_insumos[$dia]['guia_consignacion'] += $insumo->coste;
                } elseif ($insumo->insumo_id == 5) {
                    $totales_insumos[$dia]['portaguia'] += $insumo->coste;
                }
            }

                //totales apartados
                $totales_apartados = [];
                foreach($this->apartados as $apartado){
                $dia = Carbon::parse($apartado->created_at)->format('l');
                
                if(!array_key_exists($dia, $dias)){
                    continue;
                }

                if(!isset($totales_apartados[$dia])){
                    $totales_apartados[$dia] = [
                        'total_coste' => 0,
                    ];
                }

                $totales_apartados[$dia]['total_coste'] += $apartado->coste / 1.16;
            }

                //totales entregas
                $totales_entregas = [];
                foreach($this->entregas as $entrega){
                $dia = Carbon::parse($entrega->created_at)->format('l');

                if(!array_key_exists($dia, $dias)){
                    continue;
                }

                if(!isset($totales_entregas[$dia])){
                    $totales_entregas[$dia] = [
                        'total_almacenaje' => 0,
                        'total_lista_correo' => 0,
                        'total_avisos' => 0,
                        'total_500gr' => 0,
                    ];
                }

                $totales_entregas[$dia]['total_almacenaje'] += $entrega->coste_almacenaje;
                $totales_entregas[$dia]['total_lista_correo'] += $entrega->lista_correo;
                $totales_entregas[$dia]['total_avisos'] += $entrega->coste_aviso;
                if($entrega->tipo_costo_entrega == 'internacional 500gr'){
                    $totales_entregas[$dia]['total_500gr'] += $entrega->monto_cobro_extra;
                }

            }


                $sheet = $event->sheet->getDelegate();

                // --- ENCABEZADO INSTITUCIONAL ---
                $sheet->mergeCells('A1:F2');
                $drawing = new Drawing();
                $drawing->setName('cintillo');
                $drawing->setDescription('Encabezado institucional');
                $drawing->setPath(public_path('images/cintillo.jpg')); // Ruta de tu imagen
                $drawing->setHeight(95); // ajustar alto de imagen
                $drawing->setWidth(520); //ajustar ancho de imagen
                $drawing->setCoordinates('A1'); // Posición donde se colocará
                $drawing->setOffsetX(10); // Opcional: espacio desde el borde izquierdo
                $drawing->setOffsetY(5);  // Opcional: espacio desde el borde superior
                $drawing->setWorksheet($sheet);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
                //alto de las filas
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(10);
                $sheet->getColumnDimension('D')->setWidth(10);
                $sheet->getColumnDimension('E')->setWidth(11);
                $sheet->getColumnDimension('F')->setWidth(11);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(10);
                $sheet->getColumnDimension('I')->setWidth(10);
                $sheet->getColumnDimension('J')->setWidth(6);
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(20);

                for ($row = 10; $row <= 25; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(20); // Ajusta el número según lo que necesites
                }

                for($row = 7; $row <= 32; $row++){
                    $sheet->mergeCells("H{$row}:I{$row}");
                }

                $sheet->getStyle('A3:J6')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('G1:J2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A7:J32')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A33:J38')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // SUBENCABEZADO DE INFORMACIÓN

                $sheet->mergeCells('A3:D4');
                $sheet->setCellValue('A3', "PLANILLA 044\nResumen de ingresos semanales");
                $sheet->getStyle('A3')->getFont()->setSize(15);
                $sheet->getStyle('A3')->getAlignment()->setWrapText(true);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('E3:F3');
                $sheet->setCellValue('E3', 'NOMBRE OFICINA:');
                $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('E4:F4');
                $sheet->setCellValue('E4', $this->oficina['nombre']);
                $sheet->getStyle('E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->setCellValue('G1', 'SEMANA');
                $sheet->setCellValue('G2', $this->semana);
                $sheet->getStyle('G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('G3', 'Cod. Postal:'.' '.$this->oficina['codigo_ubicacion']);
                $sheet->getStyle('G3')->getFont()->setSize(8);

                $sheet->setCellValue('G4', 'Cod. Oficina:'.' '.$this->oficina['codigo']);
                $sheet->getStyle('G4')->getFont()->setSize(8);

                $sheet->mergeCells('H1:J1');
                $sheet->setCellValue('H1', 'CORRELATIVO (uso de contabilidad)');
                $sheet->getStyle('H1')->getFont()->setSize(10);
                $sheet->getStyle('H1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('H2:J2');
                
                $sheet->mergeCells('H3:H4');
                $sheet->setCellValue('H3', 'Fecha:');
                $sheet->getStyle('H3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('I3:J3');
                $sheet->setCellValue('I3', 'Desde:'.' '.$this->desde);
                $sheet->getStyle('I3')->getFont()->setSize(10);

                $sheet->mergeCells('I4:J4');
                $sheet->setCellValue('I4', 'Hasta:'.' '.$this->hasta);
                $sheet->getStyle('I4')->getFont()->setSize(10);
                
                $sheet->mergeCells('C5:G5');
                $sheet->setCellValue('C5', 'REPORTE DIARIO');
                $sheet->getStyle('C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('C6', 'LUNES');
                $sheet->getStyle('C6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('D6', 'MARTES');
                $sheet->getStyle('D6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('E6', 'MIERCOLES');
                $sheet->getStyle('E6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('F6', 'JUEVES');
                $sheet->getStyle('F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('G6', 'VIERNES');
                $sheet->getStyle('G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('H5:I6');
                $sheet->setCellValue('H5', 'TOTAL INGRESOS SEMANAL');
                $sheet->getStyle('H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H5')->getFont()->setSize(10);
                $sheet->getStyle('H5')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('J5:J6');
                $sheet->setCellValue('J5', 'COD');
                $sheet->getStyle('J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                //cambiar tamaño de fuente
                $sheet->getStyle('A5:B24')->applyFromArray([
                    'font' => [
                        'size' => 11, 
                    ],
                ]);


                $sheet->mergeCells('A5:B6');
                $sheet->setCellValue('A5', 'CONCEPTO (Servicios)');
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('A7:B7');
                $sheet->setCellValue('A7', 'Almacenaje');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales_entregas[$dia]['total_almacenaje'] ?? 0;
                    $sheet->setCellValue("{$columna}7", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A8:B8');
                $sheet->setCellValue('A8', 'Apartados');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales_apartados[$dia]['total_coste'] ?? 0;
                    $sheet->setCellValue("{$columna}8", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A9:B9');
                $sheet->setCellValue('A9', 'Aviso de Llegada');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales_entregas[$dia]['total_avisos'] ?? 0;
                    $sheet->setCellValue("{$columna}9", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A10:B10');
                $sheet->setCellValue('A10', 'Certificado Nacional e Internacional');

                $sheet->mergeCells('A11:B11');
                $sheet->setCellValue('A11', 'E.E.B.');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales[$dia]['expreso'] ?? 0;
                    $sheet->setCellValue("{$columna}11", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A12:B12');
                $sheet->setCellValue('A12', 'E.E.B. Urbano');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales[$dia]['expreso_urbano'] ?? 0;
                    $sheet->setCellValue("{$columna}12", bcdiv($valor, 1, 2));
                }
                
                $sheet->mergeCells('A13:B13');
                $sheet->setCellValue('A13', 'Entrega Paq. + 500gr');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales_entregas[$dia]['total_500gr'] ?? 0;
                    $sheet->setCellValue("{$columna}13", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A14:B14');
                $sheet->setCellValue('A14', 'Envíos Internacionales');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales[$dia]['internacional'] ?? 0;
                    $sheet->setCellValue("{$columna}14", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A15:B15');
                $sheet->setCellValue('A15', 'Envíos Nacionales');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales[$dia]['nacional'] ?? 0;
                    $sheet->setCellValue("{$columna}15", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A16:B16');
                $sheet->setCellValue('A16', 'Guías de Consignación');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales_insumos[$dia]['guia_consignacion'] ?? 0;
                    $sheet->setCellValue("{$columna}16", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A17:B17');
                $sheet->setCellValue('A17', 'Lista de Correo');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales_entregas[$dia]['total_lista_correo'] ?? 0;
                    $sheet->setCellValue("{$columna}17", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A18:B18');
                $sheet->setCellValue('A18', 'Materiales Telegramas');

                $sheet->mergeCells('A19:B19');
                $sheet->setCellValue('A19', 'Porta Guías');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales_insumos[$dia]['portaguia'] ?? 0;
                    $sheet->setCellValue("{$columna}19", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A20:B20');
                $sheet->setCellValue('A20', 'Presentación Aduana');

                $sheet->mergeCells('A21:B21');
                $sheet->setCellValue('A21', 'Servicio Logístico PYME');

                $sheet->mergeCells('A22:B22');
                $sheet->setCellValue('A22', 'Sobres Postales');

                $sheet->mergeCells('A23:B23');
                $sheet->setCellValue('A23', 'Sobres Varios');

                $sheet->mergeCells('A24:B24');
                $sheet->setCellValue('A24', 'Telegramas');
                foreach (array_keys($dias) as $i => $dia) {
                    $columna = $columnas[$i];
                    $valor = $totales[$dia]['telegrama'] ?? 0;
                    $sheet->setCellValue("{$columna}24", bcdiv($valor, 1, 2));
                }

                $sheet->mergeCells('A25:B25');

                $sheet->mergeCells('A26:B26');

                $sheet->mergeCells('A27:B27');

                $sheet->mergeCells('A28:B28');

                $sheet->mergeCells('A29:B29');


                for ($row = 7; $row <= 24; $row++) {
                $sheet->setCellValue("H{$row}", "=SUM(C{$row}:G{$row})");
                $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }





                $sheet->mergeCells('A30:B30');
                $sheet->setCellValue('A30', 'SUB TOTAL');
                $sheet->setCellValue('C30', "=SUM(C7:C24)");
                $sheet->setCellValue('D30', "=SUM(D7:D24)");
                $sheet->setCellValue('E30', "=SUM(E7:E24)");
                $sheet->setCellValue('F30', "=SUM(F7:F24)");
                $sheet->setCellValue('G30', "=SUM(G7:G24)");
                $sheet->setCellValue('H30', "=SUM(H7:H24)");
                $sheet->getStyle('G30')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G30")->getNumberFormat()->setFormatCode('0.00');
                for($col = 'C'; $col <= 'H'; $col++){
                    $sheet->getStyle("{$col}30")->getNumberFormat()->setFormatCode('0.00');
                    $sheet->getStyle("{$col}30")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->mergeCells('A31:B31');
                $sheet->setCellValue('A31', 'IVA 16%');
                $sheet->setCellValue('C31', "=C30 * 0.16");
                $sheet->setCellValue('D31', "=D30 * 0.16");
                $sheet->setCellValue('E31', "=E30 * 0.16");
                $sheet->setCellValue('F31', "=F30 * 0.16");
                $sheet->setCellValue('G31', "=G30 * 0.16");
                $sheet->setCellValue('H31', "=H30 * 0.16");
                $sheet->getStyle("G31")->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle("G31")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                for($col = 'C'; $col <= 'H'; $col++){
                    $sheet->getStyle("{$col}31")->getNumberFormat()->setFormatCode('0.00');
                    $sheet->getStyle("{$col}31")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                

                $sheet->mergeCells('A32:B32');
                $sheet->setCellValue('A32', 'TOTAL DEPOSITO CON IVA');
                $sheet->setCellValue('C32', "=SUM(C30:C31)");
                $sheet->setCellValue('D32', "=SUM(D30:D31)");
                $sheet->setCellValue('E32', "=SUM(E30:E31)");
                $sheet->setCellValue('F32', "=SUM(F30:F31)");
                $sheet->setCellValue('G32', "=SUM(G30:G31)");
                $sheet->setCellValue('H32', "=SUM(H30:H31)");
                $sheet->getStyle("G32")->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle("G32")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                for($col = 'C'; $col <= 'H'; $col++){
                    $sheet->getStyle("{$col}32")->getNumberFormat()->setFormatCode('0.00');
                    $sheet->getStyle("{$col}32")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->mergeCells('A33:J34');
                $sheet->setCellValue('A33', 'OBSERVACION:');
                $sheet->getStyle('A33')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                $sheet->mergeCells('A35:C38');
                $sheet->setCellValue('A35', 'Nombre del Supervisor Legible');
                $sheet->getStyle('A35')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A35')->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);

                $sheet->mergeCells('D35:F38');
                $sheet->setCellValue('D35', 'Firma y Sello');
                $sheet->getStyle('D35')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D35')->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);

                $sheet->mergeCells('G35:J35');
                $sheet->setCellValue('G35', 'Validar Depósitos ante Tesorería');
                $sheet->getStyle('G35')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('G36:J36');
                $sheet->setCellValue('G36', 'Nombre:');
                $sheet->mergeCells('G37:J37');
                $sheet->setCellValue('G37', 'Fecha:');
                $sheet->mergeCells('G38:J38');
                $sheet->setCellValue('G38', 'Hora:');

                for($row = 7; $row <= 24; $row++){
                    $sheet->getStyle("A{$row}")->getFont()->setSize(10);
                }

            },
        ];
    }
    public function title(): string
    {
        return 'Formato 044';
    }
}
