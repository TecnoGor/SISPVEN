<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Insumo;
use App\Models\RespaldoInventarioDiario;
use LaravelLang\Lang\Plugins\Fortify\V1;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Color;


class Formato043Export implements WithEvents, WithTitle
{
    protected $usuario;
    protected $oficina;
    protected $fecha;
    protected $envios;
    protected $insumos;
    protected $apartados;
    protected $entregas;
    protected $cantidad_avisos;
    protected $insumos_final;
    protected $insumos_asignados;
    protected $insumos_precios;
    protected $inventario_inicial;

    public function __construct($usuario, $oficina, $fecha, $envios, $insumos, $apartados, 
    $entregas, $cantidad_avisos, $insumos_final, $insumos_asignados, $insumos_precios, $inventario_inicial)
    {
        $this->usuario = $usuario;
        $this->oficina = $oficina;
        $this->fecha = $fecha;
        $this->envios = $envios;
        $this->insumos = $insumos;
        $this->apartados = $apartados;
        $this->entregas = $entregas;
        $this->cantidad_avisos = $cantidad_avisos;
        $this->insumos_final = $insumos_final;
        $this->insumos_asignados = $insumos_asignados;
        $this->insumos_precios = $insumos_precios;
        $this->inventario_inicial = $inventario_inicial;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $precios = [
                    'caja_eeb_5kg' => ['precio' => 0],
                    'guia_consignacion' => ['precio' => 0],
                    'portaguia' => ['precio' => 0], 
                ];

                foreach($this->insumos_precios as $precio){
                    if($precio->insumo_id == 2){
                        $precios['caja_eeb_5kg']['precio'] = $precio->costo;
                    }elseif($precio->insumo_id == 4){
                        $precios['guia_consignacion']['precio'] = $precio->costo;
                    }elseif($precio->insumo_id == 5){
                        $precios['portaguia']['precio'] = $precio->costo;
                    }
                }

                $servicio_envios = $this->envios->pluck('servicio_id', 'envio_id')->toArray();

                $estadisticas = [
                'servicio_urbano' => [
                    'cantidad' => 0,
                    'total_coste' => 0,
                ],
                'servicio_nacional' => [
                    'cantidad' => 0,
                    'total_coste' => 0,
                ],
                'ems' => [
                    'cantidad' => 0,
                    'total_coste' => 0,
                ],
                'telegrama' => [
                    'cantidad' => 0,
                    'total_coste' => 0,
                ],
            ];

            foreach ($this->envios as $envio) {
            if ($envio->servicio_id == 9) {
                if ($envio->servicio_expreso == 'Urbano') {
                    $estadisticas['servicio_urbano']['total_coste'] += $envio->coste_sin_iva;
                    $estadisticas['servicio_urbano']['cantidad']++;
                } elseif ($envio->servicio_expreso == 'Nacional') {
                    $estadisticas['servicio_nacional']['cantidad']++;
                    $estadisticas['servicio_nacional']['total_coste'] += $envio->coste_sin_iva;
                }
            } elseif ($envio->servicio_id == 21) {
                $estadisticas['ems']['cantidad']++;
                $estadisticas['ems']['total_coste'] += $envio->coste_sin_iva;
            } elseif ($envio->servicio_id == 3) {
                $estadisticas['telegrama']['cantidad']++;
                $estadisticas['telegrama']['total_coste'] += $envio->coste_sin_iva;
            }
        }

            $estadisticas_insumos = [
            'caja_eeb_5kg' => ['cantidad' => 0, 'total_coste' => 0],
            'portaguias' => ['cantidad' => 0, 'total_coste' => 0],
            'guia_consig_eeb' => ['cantidad' => 0, 'total_coste' => 0], 
            'guia_consig_ems' => ['cantidad' => 0, 'total_coste' => 0], 
            ];

            foreach ($this->insumos as $insumo) {
            $servicio = $servicio_envios[$insumo->envio_id] ?? null;

            if ($insumo->insumo_id == 2) {
                $estadisticas_insumos['caja_eeb_5kg']['cantidad']++;
                $estadisticas_insumos['caja_eeb_5kg']['total_coste'] += $insumo->coste;
            } elseif ($insumo->insumo_id == 5) {
                $estadisticas_insumos['portaguias']['cantidad']++;
                $estadisticas_insumos['portaguias']['total_coste'] += $insumo->coste;
            } elseif ($insumo->insumo_id == 4) {
                if ($servicio == 9) {
                    $estadisticas_insumos['guia_consig_eeb']['cantidad']++;
                    $estadisticas_insumos['guia_consig_eeb']['total_coste'] += $insumo->coste;
                } elseif ($servicio == 21) {
                    $estadisticas_insumos['guia_consig_ems']['cantidad']++;
                    $estadisticas_insumos['guia_consig_ems']['total_coste'] += $insumo->coste;
                }
            }
        }

            $estadisticas_apartados = [
                'natural' =>['cantidad' => 0, 'total_coste' => 0],
                'juridico' =>['cantidad' => 0, 'total_coste' => 0],
            ];

            foreach($this->apartados as $apartado){

                if (in_array($apartado->tipo_documento, ['V', 'E'])) {
                    $estadisticas_apartados['natural']['cantidad']++;
                    $estadisticas_apartados['natural']['total_coste'] += $apartado->coste;
                }else{
                    $estadisticas_apartados['juridico']['cantidad']++;
                    $estadisticas_apartados['juridico']['total_coste'] += $apartado->coste;
                }
            }
            $natural_sin_iva = bcdiv((string)$estadisticas_apartados['natural']['total_coste'], '1.16', 2);
            $juridico_sin_iva = bcdiv((string)$estadisticas_apartados['juridico']['total_coste'], '1.16', 2);

            $estadisticas_entregas = [
                'lista_correo' =>['cantidad' => 0, 'total_coste' => 0],
                'entrega_500_gr' =>['cantidad' => 0, 'total_coste' => 0],
                'dias_almacenaje' =>['cantidad' => 0, 'total_coste' => 0],
                'aviso_llegada' =>['cantidad' => 0, 'total_coste' => 0],
            ];

            foreach($this->entregas as $entrega){
                if($entrega->lista_correo){
                    $estadisticas_entregas['lista_correo']['cantidad']++;
                    $estadisticas_entregas['lista_correo']['total_coste']+= $entrega->lista_correo;
                }

                if($entrega->tipo_cobro_extra == 'internacional 500gr'){
                    $estadisticas_entregas['entrega_500_gr']['cantidad']++;
                    $estadisticas_entregas['entrega_500_gr']['total_coste']+= $entrega->monto_cobro_extra;
                }

                $estadisticas_entregas['dias_almacenaje']['cantidad']+= $entrega->dias_almacenaje;
                $estadisticas_entregas['dias_almacenaje']['total_coste']+= $entrega->coste_almacenaje;

                $estadisticas_entregas['aviso_llegada']['cantidad'] = $this->cantidad_avisos;
                $estadisticas_entregas['aviso_llegada']['total_coste']+= $entrega->coste_aviso;
            }

            $existencia_final = [
                'guia_consignacion' => ['cantidad' => 0, 'total_coste' => 0],
                'caja_eeb_5kg' => ['cantidad' => 0, 'total_coste' => 0],
                'portaguia' => ['cantidad' => 0, 'total_coste' => 0],
            ];

            foreach($this->insumos_final as $insumo){
                if($insumo->insumo_id == 4){
                    $existencia_final['guia_consignacion']['cantidad']+= $insumo->cantidad;
                }elseif($insumo->insumo_id == 2){
                    $existencia_final['caja_eeb_5kg']['cantidad']+= $insumo->cantidad;
                }elseif($insumo->insumo_id == 5){
                    $existencia_final['portaguia']['cantidad']+= $insumo->cantidad;
                }
            }

            $insumo_asignado = [
                'caja_eeb_5kg' => ['cantidad' => 0, 'total' => 0],
                'guia_consignacion' => ['cantidad' => 0, 'total' => 0],
                'portaguia' => ['cantidad' => 0, 'total' => 0],
            ];

            foreach($this->insumos_asignados as $insumo){
                if($insumo->insumo_id == 2){
                        $insumo_asignado['caja_eeb_5kg']['cantidad']+= $insumo->cantidad;
                        $insumo_asignado['caja_eeb_5kg']['total']+= $insumo->cantidad * $insumo->coste;
                    }elseif($insumo->insumo_id == 4){
                        $insumo_asignado['guia_consignacion']['cantidad']+= $insumo->cantidad;
                        $insumo_asignado['guia_consignacion']['total']+= $insumo->cantidad * $insumo->coste;
                    }elseif($insumo->insumo_id == 5){
                        $insumo_asignado['portaguia']['cantidad']+= $insumo->cantidad;
                        $insumo_asignado['portaguia']['total']+= $insumo->cantidad * $insumo->coste;
                    }
            }

            $inicial = [
                'caja_eeb_5kg' => ['cantidad' => 0, 'total' => 0],
                'guia_consignacion' => ['cantidad' => 0, 'total' => 0],
                'portaguia' => ['cantidad' => 0, 'total' => 0],
            ];

            foreach($this->inventario_inicial as $inv){
                if($inv->insumo_id == 2){
                    $inicial['caja_eeb_5kg']['cantidad']+= $inv->cantidad;
                    $inicial['caja_eeb_5kg']['total']+= $inv->cantidad * $inv->coste;
                }elseif($inv->insumo_id == 4){
                    $inicial['guia_consignacion']['cantidad']+= $inv->cantidad;
                    $inicial['guia_consignacion']['total']+= $inv->cantidad * $inv->coste;
                }elseif($inv->insumo_id == 5){
                    $inicial['portaguia']['cantidad']+= $inv->cantidad;
                    $inicial['portaguia']['total']+= $inv->cantidad * $inv->coste;
                }
            }


                $sheet = $event->sheet->getDelegate();

                // --- ENCABEZADO INSTITUCIONAL ---
                $sheet->mergeCells('A1:W1');
                $drawing = new Drawing();
                $drawing->setResizeProportional(false);
                $drawing->setName('cintillo');
                $drawing->setDescription('Encabezado institucional');
                $drawing->setPath(public_path('images/cintillo.jpg')); // Ruta de imagen
                $drawing->setHeight(45); // ajustar alto de imagen
                $drawing->setWidth(570); //ajustar ancho de imagen
                $drawing->setCoordinates('A1'); // Posición
                $drawing->setOffsetX(10); // espacio desde el borde izquierdo
                $drawing->setOffsetY(5);  // espacio desde el borde superior
                $drawing->setWorksheet($sheet);
                $sheet->getStyle('A11:I33')->getFont()->setBold(FALSE)->setSize(8);
                $sheet->getStyle('A3:I10')->getFont()->setBold(FALSE)->setSize(10);
                //alto de las filas
                $sheet->getColumnDimension('A')->setWidth(26);

                foreach (range('B', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setWidth(7);
                }

                foreach (range('B12', 'I30') as $col) {
                    $sheet->getStyle($col)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->getRowDimension(1)->setRowHeight(25);

                $borderStyle = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => 'FF000000'], // negro
                        ],
                    ],
                ];

                $redFontStyle = [
                    'font' => [
                        'color' => ['argb' => 'FFFF0000'], // rojo
                    ],
                ];

                //aplicar lineas mas gruesas a las celdas del encabezado
                $sheet->getStyle('A3:I10')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A11:I30')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('D31:G33')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);


                


                // SUBENCABEZADO DE INFORMACIÓN

                $sheet->mergeCells('A3:G6');
                $sheet->setCellValue('A3', 'MOVIMIENTO DIARIO DE VALORES');
                $sheet->getStyle('A3')->getFont()->setSize(16);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('H3:I3');
                $sheet->setCellValue('H3', 'SEMANA'.' '.Carbon::parse($this->fecha)->weekOfYear);
                $sheet->getStyle('H3')->getFont()->setSize(10);
                $sheet->getStyle('H3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('H4:I4');

                $sheet->mergeCells('H5:I6');
                $sheet->setCellValue('H5', $this->fecha);
                $sheet->getStyle('H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('A7:D7');
                $sheet->setCellValue('A7', 'NOMBRE Y APELLIDO:');
                $sheet->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('E7:G7');
                $sheet->setCellValue('E7', 'CARGO:');
                $sheet->getStyle('E7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('H7:I7');

                $sheet->mergeCells('A8:D8');
                $sheet->setCellValue('A8', $this->usuario['name']);
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('E8:G8');
                $sheet->setCellValue('E8', $this->usuario->getRoleNames()->first());
                $sheet->getStyle('E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('H8:I8');
                $sheet->setCellValue('H8', 'VENTA 1');
                $sheet->getStyle('H8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A9:D9');
                $sheet->setCellValue('A9', 'OFICINA:');
                $sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('E9:G9');
                $sheet->setCellValue('E9', 'ESTADO:');
                $sheet->getStyle('E9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('H9:I9');
                $sheet->setCellValue('H9', 'REGION:');
                $sheet->getStyle('H9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A10:D10');
                $sheet->setCellValue('A10', $this->oficina['nombre']);
                $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('E10:G10');
                $sheet->setCellValue('E10', $this->oficina->estado->nombre);
                $sheet->getStyle('E10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('H10:I10');
                $sheet->setCellValue('H10', $this->oficina->estado->region->nombre);
                $sheet->getStyle('H10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A11:A12');
                $sheet->setCellValue('A11', 'DENOMINACIÓN');
                $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A11')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('B11:C11');
                $sheet->setCellValue('B11', 'EXISTENCIA INICIAL');
                $sheet->setCellValue('B12', 'UNID');
                $sheet->setCellValue('C12', 'BS');
                $sheet->getStyle('B11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('D11:E11');
                $sheet->setCellValue('D11', 'VALORES RECIBIDOS');
                $sheet->setCellValue('D12', 'UNID');
                $sheet->setCellValue('E12', 'BS');
                $sheet->getStyle('D11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('F11:G11');
                $sheet->setCellValue('F11', 'VENTAS');
                $sheet->setCellValue('F12', 'UNID');
                $sheet->setCellValue('G12', 'BS');
                $sheet->getStyle('F11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('H11:I11');
                $sheet->setCellValue('H11', 'EXISTENCIA FINAL');
                $sheet->setCellValue('H12', 'UNID');
                $sheet->setCellValue('I12', 'BS');
                $sheet->getStyle('H11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                //DENOMINACION LISTA
                $sheet->setCellValue('A13', 'GUIA DE CONSIGNACION EMS');
                $sheet->setCellValue('F13', $estadisticas_insumos['guia_consig_ems']['cantidad']);
                $sheet->setCellValue('G13', $estadisticas_insumos['guia_consig_ems']['total_coste']);

                $sheet->setCellValue('A14', 'EMS (INTERNACIONAL)');
                $sheet->setCellValue('F14', $estadisticas['ems']['cantidad']);
                $sheet->setCellValue('G14', $estadisticas['ems']['total_coste']);

                $sheet->setCellValue('A15', 'E. EXPRESO BOLIVARIANO (URBANO)');
                $sheet->setCellValue('F15', $estadisticas['servicio_urbano']['cantidad']);
                $sheet->setCellValue('G15', $estadisticas['servicio_urbano']['total_coste']);

                $sheet->setCellValue('A16', 'E. EXPRESO BOLIVARIANO (NACIONAL)');
                $sheet->setCellValue('F16', $estadisticas['servicio_nacional']['cantidad']);
                $sheet->setCellValue('G16', $estadisticas['servicio_nacional']['total_coste']);

                $sheet->setCellValue('A17', 'GUIA DE CONSIGNACION EEB');
                $sheet->setCellValue('B17', $inicial['guia_consignacion']['cantidad']);
                $sheet->setCellValue('C17', $inicial['guia_consignacion']['total']);
                $sheet->setCellValue('D17', $insumo_asignado['guia_consignacion']['cantidad']);
                $sheet->setCellValue('E17', $insumo_asignado['guia_consignacion']['total']);
                $sheet->setCellValue('F17', $estadisticas_insumos['guia_consig_eeb']['cantidad']);
                $sheet->setCellValue('G17', $estadisticas_insumos['guia_consig_eeb']['total_coste']);
                $sheet->setCellValue('H17', $existencia_final['guia_consignacion']['cantidad']);
                $sheet->setCellValue('I17', $existencia_final['guia_consignacion']['cantidad'] * $precios['guia_consignacion']['precio']);

                $sheet->setCellValue('A18', 'PORTAGUIAS EEE/ EEB');
                $sheet->setCellValue('B18', $inicial['portaguia']['cantidad']);
                $sheet->setCellValue('C18', $inicial['portaguia']['total']);
                $sheet->setCellValue('D18', $insumo_asignado['portaguia']['cantidad']);
                $sheet->setCellValue('E18', $insumo_asignado['portaguia']['total']);
                $sheet->setCellValue('F18', $estadisticas_insumos['portaguias']['cantidad']);
                $sheet->setCellValue('G18', $estadisticas_insumos['portaguias']['total_coste']);
                $sheet->setCellValue('H18', $existencia_final['portaguia']['cantidad']);
                $sheet->setCellValue('I18', $existencia_final['portaguia']['cantidad'] * $precios['portaguia']['precio']);


                $sheet->setCellValue('A19', 'SOBRE GRIS SIN PORTAGUIA');

                $sheet->setCellValue('A20', 'CAJAS EEB DE 5 KG');
                $sheet->setCellValue('B20', $inicial['caja_eeb_5kg']['cantidad']);
                $sheet->setCellValue('C20', $inicial['caja_eeb_5kg']['total']);
                $sheet->setCellValue('D20', $insumo_asignado['caja_eeb_5kg']['cantidad']);
                $sheet->setCellValue('E20', $insumo_asignado['caja_eeb_5kg']['total']);
                $sheet->setCellValue('F20', $estadisticas_insumos['caja_eeb_5kg']['cantidad']);
                $sheet->setCellValue('G20', $estadisticas_insumos['caja_eeb_5kg']['total_coste']);
                $sheet->setCellValue('H20', $existencia_final['caja_eeb_5kg']['cantidad']);
                $sheet->setCellValue('I20', $existencia_final['caja_eeb_5kg']['cantidad'] * $precios['caja_eeb_5kg']['precio']);

                $sheet->setCellValue('A21', 'LISTA CORREO');
                $sheet->setCellValue('F21', $estadisticas_entregas['lista_correo']['cantidad']);
                $sheet->setCellValue('G21', $estadisticas_entregas['lista_correo']['total_coste']);

                $sheet->setCellValue('A22', 'APARTADOS NATURAL');
                $sheet->setCellValue('F22', $estadisticas_apartados['natural']['cantidad']);
                $sheet->setCellValue('G22', $natural_sin_iva);

                $sheet->setCellValue('A23', 'APARTADOS JURIDICO');
                $sheet->setCellValue('F23', $estadisticas_apartados['juridico']['cantidad']);
                $sheet->setCellValue('G23', $juridico_sin_iva);

                $sheet->setCellValue('A24', 'TELEGRAMA');
                $sheet->setCellValue('F24', $estadisticas['telegrama']['cantidad']);
                $sheet->setCellValue('G24', $estadisticas['telegrama']['total_coste']);

                $sheet->setCellValue('A25', 'ENTREGA A DESTINATARIO PAQ + 500gr');
                $sheet->setCellValue('F25', $estadisticas_entregas['entrega_500_gr']['cantidad']);
                $sheet->setCellValue('G25', $estadisticas_entregas['entrega_500_gr']['total_coste']);

                $sheet->setCellValue('A26', 'DIAS DE ALMACENAJE');
                $sheet->setCellValue('F26', $estadisticas_entregas['dias_almacenaje']['cantidad']);
                $sheet->setCellValue('G26', $estadisticas_entregas['dias_almacenaje']['total_coste']);

                $sheet->setCellValue('A27', 'AVISO DE LLEGADA');
                $sheet->setCellValue('F27', $estadisticas_entregas['aviso_llegada']['cantidad']);
                $sheet->setCellValue('G27', $estadisticas_entregas['aviso_llegada']['total_coste']);

                $sheet->setCellValue('A28', 'PRESENTACION DE ADUANA');


                $sheet->setCellValue('A30', 'TOTALES');
                $sheet->getStyle('A30')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('B30', '=SUM(B13:B28)');
                $sheet->setCellValue('C30', '=SUM(C13:C28)');
                $sheet->setCellValue('D30', '=SUM(D13:D28)');
                $sheet->setCellValue('E30', '=SUM(E13:E28)');
                $sheet->setCellValue('H30', '=SUM(H13:H28)');
                $sheet->setCellValue('I30', '=SUM(I13:I28)');



                $sheet->mergeCells('D31:F31');
                $sheet->setCellValue('D31', 'SUB TOTAL VENTAS');
                $sheet->setCellValue('G31', '=SUM(G13:G28)');

                $sheet->mergeCells('D32:F32');
                $sheet->setCellValue('D32', 'IMPUESTO I.V.A 16%');
                $sheet->setCellValue('G32', '=SUM(G13:G28) * 0.16');

                $sheet->mergeCells('D33:F33');
                $sheet->setCellValue('D33', 'TOTAL GENERAL');
                $sheet->setCellValue('G33', '=G31 + G32');

                $sheet->getStyle('G31:G33')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

                $sheet->getStyle('D31:G33')->getFont()->getColor()->setARGB(Color::COLOR_BLUE);






                


                







                
















                //Estilo de recuadro negro//                
                // $sheet->getStyle('A14:C16')->applyFromArray($borderStyle);

                // $sheet->getStyle('D14:E16')->applyFromArray($borderStyle);

                // $sheet->getStyle('F14:H16')->applyFromArray($borderStyle);

                // $sheet->getStyle('I14:J16')->applyFromArray($borderStyle);


            },
        ];
    }

    public function title(): string
    {
        return 'SEMANA'.' '.Carbon::parse($this->fecha)->weekOfYear;
    }
    
}
