<?php

namespace App\Exports;

use Carbon\Carbon;
use LaravelLang\Lang\Plugins\Fortify\V1;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Formato047 implements WithEvents, WithTitle
{

    protected $desde, $hasta, $semana, $oficina, $envios_id, $insumos, $inventario_final, 
    $inventario_inicial, $recibidos, $insumos_dev, $envios_devolucion_id;

    public function __construct($desde, $hasta, $semana, $oficina, $envios_id, $insumos, 
    $inventario_final, $inventario_inicial, $recibidos, $insumos_dev, $envios_devolucion_id){

        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->semana = $semana;
        $this->oficina = $oficina;
        $this->envios_id = $envios_id;
        $this->insumos = $insumos;
        $this->inventario_final = $inventario_final;
        $this->inventario_inicial = $inventario_inicial;
        $this->recibidos = $recibidos;
        $this->insumos_dev = $insumos_dev;
        $this->envios_devolucion_id = $envios_devolucion_id;

    }

    public function registerEvents(): array
    {
        return[
            AfterSheet::class => function (AfterSheet $event) {

                $insumo_map = [
                    2 => 'cajas_eeb_5kg',
                    3 => 'cajas_eeb_3kg',
                    4 => ['guia_ems' => 24, 'guia_eeb' => 9],
                    5 => 'portaguias',
                    8 => 'sobre_carta',
                    9 => 'sobre_media_carta',
                    11 => 'hojas_tamaño_carta',
                    13 => 'precintos',
                    14 => 'bolsa_plastica_2kg',
                    15 => 'bolsa_plastica_5kg',
                    16 => 'bolsa_plastica_10kg',
                ];


            $insumos_ventas = [];
            foreach ($this->insumos as $insumo) {
                $insumo_id = $insumo->insumo_id;
                $coste = $insumo->coste;
                $servicio = $this->envios_id[$insumo->envio_id] ?? null;

                if ($insumo_id == 4) {
                    foreach ($insumo_map[4] as $nombre => $tipo_servicio) {
                        if ($servicio == $tipo_servicio) {
                            $insumos_ventas[$nombre]['total'] = ($insumos_ventas[$nombre]['total'] ?? 0) + $coste;
                        }
                    }
                } elseif (isset($insumo_map[$insumo_id])) {
                    $nombre = $insumo_map[$insumo_id];
                    $insumos_ventas[$nombre]['total'] = ($insumos_ventas[$nombre]['total'] ?? 0) + $coste;
                }
            }

            $existencia_final = [];
            foreach($this->inventario_final as $final){
                $final_id = $final->insumo->insumo_id;
                $cantidad_final = $final->cantidad;
                $precio_final = $final->insumo->costo ?? 0;
                $total_final = $cantidad_final * $precio_final;

                $existencia_final[$final_id]['cantidad'] = ($existencia_final[$final_id]['cantidad'] ?? 0) + $cantidad_final;
                $existencia_final[$final_id]['total'] = ($existencia_final[$final_id]['total'] ?? 0) + $total_final;
            
            }

            $existencia_inicial = [];
            foreach($this->inventario_inicial as $inicial){
                $inicial_id = $inicial->insumo_id;
                $cantidad_inicial = $inicial->cantidad;
                $precio_inicial = $inicial->coste;
                $total_inicial = $cantidad_inicial * $precio_inicial;

                $existencia_inicial[$inicial_id]['cantidad'] = ($existencia_inicial[$inicial_id]['cantidad'] ?? 0) + $cantidad_inicial;
                $existencia_inicial[$inicial_id]['total'] = ($existencia_inicial[$inicial_id]['total'] ?? 0) + $total_inicial;
            }

            $valores_recibidos = [];
            foreach($this->recibidos as $recibido){
                $recibido_id = $recibido['insumo_id'];
                $recibido_cantidad = $recibido['cantidad'];
                $precio_recibido = $recibido['coste'];

                $recibido_total = $recibido_cantidad * $precio_recibido;
                
                $valores_recibidos[$recibido_id]['cantidad'] = ($valores_recibidos[$recibido_id]['cantidad'] ?? 0) + $recibido_cantidad;
                $valores_recibidos[$recibido_id]['total'] = ($valores_recibidos[$recibido_id]['total'] ?? 0) + $recibido_total;
            }

            $insumos_devol = [];
            foreach ($this->insumos_dev as $insumo) {
                $insumo_dev_id = $insumo->insumo_id;
                $coste_dev = $insumo->coste;
                $servicio_dev = $this->envios_devolucion_id[$insumo->envio_id] ?? null;

                if ($insumo_dev_id == 4) {
                    foreach ($insumo_map[4] as $nombre => $tipo_servicio) {
                        if ($servicio_dev == $tipo_servicio) {
                            $insumos_devol[$nombre]['total'] = ($insumos_devol[$nombre]['total'] ?? 0) + $coste_dev;
                        }
                    }
                } elseif (isset($insumo_map[$insumo_dev_id])) {
                    $nombre = $insumo_map[$insumo_dev_id];
                    $insumos_devol[$nombre]['total'] = ($insumos_devol[$nombre]['total'] ?? 0) + $coste_dev;
                }
            }






                $sheet = $event->sheet->getDelegate();

                // --- ENCABEZADO INSTITUCIONAL ---
                $sheet->mergeCells('A1:J2');
                $drawing = new Drawing();
                $drawing->setName('cintillo');
                $drawing->setDescription('Encabezado institucional');
                $drawing->setPath(public_path('images/cintillo.jpg')); // Ruta de tu imagen
                $drawing->setHeight(100); // ajustar alto de imagen
                $drawing->setWidth(790); //ajustar ancho de imagen
                $drawing->setCoordinates('A1'); // Posición donde se colocará
                $drawing->setOffsetX(10); // Opcional: espacio desde el borde izquierdo
                $drawing->setOffsetY(5);  // Opcional: espacio desde el borde superior
                $drawing->setWorksheet($sheet);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
                //alto de las filas
                $sheet->getColumnDimension('A')->setWidth(4);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(12);
                $sheet->getColumnDimension('H')->setWidth(13);
                $sheet->getColumnDimension('I')->setWidth(12);
                $sheet->getColumnDimension('J')->setWidth(12);

                $sheet->getRowDimension(1)->setRowHeight(25);

                $sheet->getStyle("A10:A25")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);


                //BORDES MAS OSCUROS PARA LOS TITULOS Y MAS DELGADOS PARA LOS DATOS
                $sheet->getStyle('A3:J39')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('E10:J25')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('E32:J33')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                

                // SUBENCABEZADO DE INFORMACIÓN

                $sheet->mergeCells('A3:F6');
                $sheet->setCellValue('A3', "PLANILLA 047\nCONTROL DE INVENTARIOS");
                $sheet->getStyle('A3')->getFont()->setSize(20);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A3')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('G3:H4');
                $sheet->setCellValue('G3', "NOMBRE DE LA OFICINA:\n" . $this->oficina['nombre']);
                $sheet->getStyle('G3')->getFont()->setSize(10);
                $sheet->getStyle('G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G3')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('G5:H6');
                $sheet->setCellValue('G5', "CODIGO DE LA OFICINA:\n" . $this->oficina['codigo']);
                $sheet->getStyle('G5')->getFont()->setSize(10);
                $sheet->getStyle('G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G5')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('I3:J3');
                $sheet->setCellValue('I3', "SEMANA:".' '.$this->semana);

                $sheet->mergeCells('I4:J4');
                $sheet->setCellValue('I4', "DESDE:". ' '.$this->desde);

                $sheet->mergeCells('I5:J5');
                $sheet->setCellValue('I5', "HASTA:".' '.$this->hasta);
                $sheet->mergeCells('I6:J6');

                $sheet->mergeCells('A7:J7');
                $sheet->setCellValue('A7', "EXPRESADO EN BS:");
                $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                //CONCEPTOS

                $sheet->mergeCells('A8:D9');
                $sheet->setCellValue('A8', "CONCEPTOS RUBLOS:");
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('E8:E9');
                $sheet->setCellValue('E8', "EXISTENCIA\nANTERIOR");
                $sheet->getStyle('E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E8')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('F8:F9');
                $sheet->setCellValue('F8', "VALORES\nRECIBIDOS");
                $sheet->getStyle('F8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F8')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('G8:G9');
                $sheet->setCellValue('G8', "TOTAL\nVENTAS");
                $sheet->getStyle('G8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G8')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('H8:H9');
                $sheet->setCellValue('H8', "DEVOLUCION");
                $sheet->getStyle('H8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H8')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('H8')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('I8:I9');
                $sheet->setCellValue('I8', "CUPON\nRESPUESTA");
                $sheet->getStyle('I8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I8')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('J8:J9');
                $sheet->setCellValue('J8', "EXISTENCIA\nFINAL");
                $sheet->getStyle('J8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J8')->getAlignment()->setWrapText(true);

                $sheet->setCellValue('A10', "1");
                $sheet->mergeCells('B10:D10');
                $sheet->setCellValue('B10', "GUIA DE CONSIGNACION EMS");
                $sheet->setCellValue('E10', $existencia_inicial[4]['total'] ?? 0);
                $sheet->setCellValue('F10', $valores_recibidos[4]['total'] ?? 0);
                $sheet->setCellValue('G10', $insumos_ventas['guia_ems']['total'] ?? 0);
                $sheet->setCellValue('H10', $insumos_devol['guia_ems']['total'] ?? 0);
                $sheet->setCellValue('J10', $existencia_final[4]['total'] ?? 0);

                $sheet->setCellValue('A11', "2");
                $sheet->mergeCells('B11:D11');
                $sheet->setCellValue('B11', "GUIA DE CONSIGNACION EEB");
                $sheet->setCellValue('E11', $existencia_inicial[4]['total'] ?? 0);
                $sheet->setCellValue('F11', $valores_recibidos[4]['total'] ?? 0);
                $sheet->setCellValue('G11', $insumos_ventas['guia_eeb']['total'] ?? 0);
                $sheet->setCellValue('H11', $insumos_devol['guia_eeb']['total'] ?? 0);
                $sheet->setCellValue('J11', $existencia_final[4]['total'] ?? 0);

                $sheet->setCellValue('A12', "3");
                $sheet->mergeCells('B12:D12');
                $sheet->setCellValue('B12', "PORTAGUIAS EEB/EMS");
                $sheet->setCellValue('E12', $existencia_inicial[5]['total'] ?? 0);
                $sheet->setCellValue('F12', $valores_recibidos[5]['total'] ?? 0);
                $sheet->setCellValue('G12', $insumos_ventas['portaguias']['total'] ?? 0);
                $sheet->setCellValue('H12', $insumos_devol['portaguias']['total'] ?? 0);
                $sheet->setCellValue('J12', $existencia_final[5]['total'] ?? 0);
                
                

                $sheet->setCellValue('A13', "4");
                $sheet->mergeCells('B13:D13');
                $sheet->setCellValue('B13', "SOBRE GRIS SIN PORTAGUIA");

                $sheet->setCellValue('A14', "5");
                $sheet->mergeCells('B14:D14');
                $sheet->setCellValue('B14', "CAJAS 10 Kg");

                $sheet->setCellValue('A15', "6");
                $sheet->mergeCells('B15:D15');
                $sheet->setCellValue('B15', "CAJAS EEB 5 Kg");
                $sheet->setCellValue('E15', $existencia_inicial[2]['total'] ?? 0);
                $sheet->setCellValue('F15', $valores_recibidos[2]['total'] ?? 0);
                $sheet->setCellValue('G15', $insumos_ventas['cajas_eeb_5kg']['total'] ?? 0);
                $sheet->setCellValue('H15', $insumos_devol['cajas_eeb_5kg']['total'] ?? 0);
                $sheet->setCellValue('J15', $existencia_final[2]['total'] ?? 0);

                $sheet->setCellValue('A16', "7");
                $sheet->mergeCells('B16:D16');
                $sheet->setCellValue('B16', "CAJAS EEB 3 Kg");
                $sheet->setCellValue('E16', $existencia_inicial[3]['total'] ?? 0);
                $sheet->setCellValue('F16', $valores_recibidos[3]['total'] ?? 0);
                $sheet->setCellValue('G16', $insumos_ventas['cajas_eeb_3kg']['total'] ?? 0);
                $sheet->setCellValue('H16', $insumos_devol['cajas_eeb_3kg']['total'] ?? 0);
                $sheet->setCellValue('J16', $existencia_final[3]['total'] ?? 0);

                $sheet->setCellValue('A17', "8");
                $sheet->mergeCells('B17:D17');
                $sheet->setCellValue('B17', "SOBRE BLANCO ALBA Nº 11");

                $sheet->setCellValue('A18', "9");
                $sheet->mergeCells('B18:D18');
                $sheet->setCellValue('B18', "SOBRE CARTA");
                $sheet->setCellValue('E18', $existencia_inicial[8]['total'] ?? 0);
                $sheet->setCellValue('F18', $valores_recibidos[8]['total'] ?? 0);
                $sheet->setCellValue('G18', $insumos_ventas['sobre_carta']['total'] ?? 0);
                $sheet->setCellValue('H18', $insumos_devol['sobre_carta']['total'] ?? 0);
                $sheet->setCellValue('J18', $existencia_final[8]['total'] ?? 0);

                $sheet->setCellValue('A19', "10");
                $sheet->mergeCells('B19:D19');
                $sheet->setCellValue('B19', "SOBRE MEDIA CARTA");
                $sheet->setCellValue('E19', $existencia_inicial[9]['total'] ?? 0);
                $sheet->setCellValue('F19', $valores_recibidos[9]['total'] ?? 0);
                $sheet->setCellValue('G19', $insumos_ventas['sobre_media_carta']['total'] ?? 0);
                $sheet->setCellValue('H19', $insumos_devol['sobre_media_carta']['total'] ?? 0);
                $sheet->setCellValue('J19', $existencia_final[9]['total'] ?? 0);

                $sheet->setCellValue('A20', "11");
                $sheet->mergeCells('B20:D20');
                $sheet->setCellValue('B20', "SOBRE PLASTICO 2(TAMAÑO OFICIO)");

                $sheet->setCellValue('A21', "12");
                $sheet->mergeCells('B21:D21');
                $sheet->setCellValue('B21', "HOJAS TAMAÑO CARTA");
                $sheet->setCellValue('E21', $existencia_inicial[11]['total'] ?? 0);
                $sheet->setCellValue('F21', $valores_recibidos[11]['total'] ?? 0);
                $sheet->setCellValue('G21', $insumos_ventas['hoja_tamaño_carta']['total'] ?? 0);
                $sheet->setCellValue('H21', $insumos_devol['hoja_tamaño_carta']['total'] ?? 0);
                $sheet->setCellValue('J21', $existencia_final[11]['total'] ?? 0);

                $sheet->setCellValue('A22', "13");
                $sheet->mergeCells('B22:D22');
                $sheet->setCellValue('B22', "PRECINTOS");
                $sheet->setCellValue('E22', $existencia_inicial[13]['total'] ?? 0);
                $sheet->setCellValue('F22', $valores_recibidos[13]['total'] ?? 0);
                $sheet->setCellValue('G22', $insumos_ventas['precintos']['total'] ?? 0);
                $sheet->setCellValue('H22', $insumos_devol['precintos']['total'] ?? 0);
                $sheet->setCellValue('J22', $existencia_final[13]['total'] ?? 0);

                $sheet->setCellValue('A23', "14");
                $sheet->mergeCells('B23:D23');
                $sheet->setCellValue('B23', "BOLSA PLASTICA 2 Kg");
                $sheet->setCellValue('E23', $existencia_inicial[14]['total'] ?? 0);
                $sheet->setCellValue('F23', $valores_recibidos[14]['total'] ?? 0);
                $sheet->setCellValue('G23', $insumos_ventas['bolsa_plastica_2kg']['total'] ?? 0);
                $sheet->setCellValue('H23', $insumos_devol['bolsa_plastica_2kg']['total'] ?? 0);
                $sheet->setCellValue('J23', $existencia_final[14]['total'] ?? 0);

                $sheet->setCellValue('A24', "15");
                $sheet->mergeCells('B24:D24');
                $sheet->setCellValue('B24', "BOLSA PLASTICA 5 Kg");
                $sheet->setCellValue('E24', $existencia_inicial[15]['total'] ?? 0);
                $sheet->setCellValue('F24', $valores_recibidos[15]['total'] ?? 0);
                $sheet->setCellValue('G24', $insumos_ventas['bolsa_plastica_5kg']['total'] ?? 0);
                $sheet->setCellValue('H24', $insumos_devol['bolsa_plastica_5kg']['total'] ?? 0);
                $sheet->setCellValue('J24', $existencia_final[15]['total'] ?? 0);

                $sheet->setCellValue('A25', "16");
                $sheet->mergeCells('B25:D25');
                $sheet->setCellValue('B25', "BOLSA PLASTICA 10 Kg");
                $sheet->setCellValue('E25', $existencia_inicial[16]['total'] ?? 0);
                $sheet->setCellValue('F25', $valores_recibidos[16]['total'] ?? 0);
                $sheet->setCellValue('G25', $insumos_ventas['bolsa_plastica_10kg']['total'] ?? 0);
                $sheet->setCellValue('H25', $insumos_devol['blosa_plastica_10kg']['total'] ?? 0);
                $sheet->setCellValue('J25', $existencia_final[16]['total'] ?? 0);

                $sheet->mergeCells('A26:D26');
                $sheet->setCellValue('A26', "TOTAL DE INVENTARIOS");
                $sheet->getStyle('A26:J26')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A26')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('E26', "=SUM(E10:E25)");
                $sheet->setCellValue('F26', "=SUM(F10:F25)");
                $sheet->setCellValue('G26', "=SUM(G10:G25)");
                $sheet->setCellValue('H26', "=SUM(H10:H25)");
                $sheet->setCellValue('J26', "=SUM(J10:J25)");
                

                $sheet->mergeCells('A27:J29');
                $sheet->setCellValue('A27', "CONTROL DE MOVIMIENTOS DE GIROS POSTALES");
                $sheet->getStyle('A27')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A30:D31');
                $sheet->setCellValue('A30', "TOTAL GIROS\nVENDIDOS BS");
                $sheet->getStyle('A30')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A30')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('E30:F31');
                $sheet->setCellValue('E30', "TRANSFERENCIAS RECIBIDAS\n(AGOFON) BS");
                $sheet->getStyle('E30')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E30')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('G30:H31');
                $sheet->setCellValue('G30', "TRANSFERENCIAS EXCEDENTES\nDEPOSITADOS BS");
                $sheet->getStyle('G30')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G30')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('I30:I31');
                $sheet->setCellValue('I30', "TOTAL GIROS\nPAGADOS BS");
                $sheet->getStyle('I30')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I30')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('J30:J31');
                $sheet->setCellValue('J30', "EXISTENCIA\nFINAL BS");
                $sheet->getStyle('J30')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J30')->getAlignment()->setWrapText(true);

                $sheet->mergeCells('A32:D39');
                $sheet->setCellValue('A32', "OBSERVACIONES:");
                $sheet->getStyle('A32')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle('A32')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->mergeCells('E34:G34');
                $sheet->setCellValue('E34', "JEFE DE OFICINA. FIRMA Y SELLO");
                $sheet->getStyle('E34')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('E35:G39');

                $sheet->mergeCells('H34:J34');
                $sheet->setCellValue('H34', "TAQUILLERO O JEFE DE DEPOSITO");
                $sheet->getStyle('H34')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('H35:J39');
                


            },
        ];
    }
    public function title(): string
    {
        return 'Semana'.' '.$this->semana;
    }
}
