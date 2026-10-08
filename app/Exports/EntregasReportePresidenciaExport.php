<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\TarifaNacionalConcepto;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use App\Models\TarifaInternacionalConcepto;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class EntregasReportePresidenciaExport implements WithEvents, WithTitle
{

    protected $envios;
    

    public function __construct($envios)
    {
        $this->envios = $envios;
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
                $sheet->getRowDimension(6)->setRowHeight(30);
                $sheet->getRowDimension(7)->setRowHeight(30);
                $sheet->getRowDimension(8)->setRowHeight(30);
                //ancho de columnas
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('K')->setWidth(15);
                $sheet->getColumnDimension('L')->setWidth(15);

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

                // SUBENCABEZADO DE INFORMACIÓN
                $sheet->mergeCells('A4:AB4');
                $sheet->setCellValue('A4', 'REPORTE DE ENTREGA DE ENVÍOS CERTIFICADOS INTERNACIONALES Y NACIONALES (LC, AO, P/P Y BULTOS)');
                $sheet->getStyle('A4:AB4')->applyFromArray($encabezado_azul);

                //Segundo Subencabezado de titulos
                $sheet->mergeCells('A6:AB6');
                $sheet->setCellValue('A6', 'ENTIDAD:');
                $sheet->getStyle('A6:AB6')->applyFromArray($encabezado_azul);

                //Tercer Subencabezado de titulos
                $sheet->mergeCells('K7:L7');
                $sheet->setCellValue('K7', 'DÍAS DE ALMACENAJE');
                $sheet->getStyle('K7:L7')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('S7:W7');
                $sheet->setCellValue('S7', 'INGRESO BS:');
                $sheet->getStyle('S7:W7')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('Z7');
                $sheet->setCellValue('Z7', 'IVA:');
                $sheet->getStyle('Z7')->applyFromArray($encabezado_azul);

                //Cuarto subencabezado de titulos
                $sheet->mergeCells('A7:A8');
                $sheet->setCellValue('A7', 'N°');
                $sheet->getStyle('A7:A8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('B7:B8');
                $sheet->setCellValue('B7', 'INDIQUE EL TIPO DE ENTREGA');
                $sheet->getStyle('B7:B8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('C7:C8');
                $sheet->setCellValue('C7', 'FECHA DE RECEPCIÓN EN OPT');
                $sheet->getStyle('C7:C8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('D7:D8');
                $sheet->setCellValue('D7', 'CÓDIGO DE ENVÍO');
                $sheet->getStyle('D7:D8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('E7:E8');
                $sheet->setCellValue('E7', 'OPT ORIGEN/ PAIS DE ORIGEN');
                $sheet->getStyle('E7:E8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('F7:F8');
                $sheet->setCellValue('F7', 'OPT ENTREGA');
                $sheet->getStyle('F7:F8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('G7:G8');
                $sheet->setCellValue('G7', 'ENTIDAD');
                $sheet->getStyle('G7:G8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('H7:H8');
                $sheet->setCellValue('H7', 'CANTIDAD DE AVISO DE LLEGADA');
                $sheet->getStyle('H7:H8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('I7:I8');
                $sheet->setCellValue('I7', 'FECHA DE ENTREGA EN OPT');
                $sheet->getStyle('I7:I8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('J7:J8');
                $sheet->setCellValue('J7', 'LISTA DE CORREO');
                $sheet->getStyle('J7:J8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('K8');
                $sheet->setCellValue('K8', 'FECHA INICIAL');
                $sheet->getStyle('K8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('L8');
                $sheet->setCellValue('L8', 'FECHA FINAL');
                $sheet->getStyle('L8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('M7:M8');
                $sheet->setCellValue('M7', 'TOTAL DÍAS');
                $sheet->getStyle('M7:M8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('N7:N8');
                $sheet->setCellValue('N7', 'PESO NACIONAL');
                $sheet->getStyle('N7:N8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('O7:O8');
                $sheet->setCellValue('O7', 'PESO INTERNACIONAL');
                $sheet->getStyle('O7:O8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('P7:P8');
                $sheet->setCellValue('P7', 'PRESENTACIÓN ADUANA');
                $sheet->getStyle('P7:P8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('Q7:Q8');
                $sheet->setCellValue('Q7', "PETICIÓN DE REEXPEDICIÓN,\n DEVOLUCIÓN O MODIFICACIÓN\n DE DIRECCIÓN (NACIONAL)");
                $sheet->getStyle('Q7:Q8')->getAlignment()->setWrapText(true);
                $sheet->getStyle('Q7:Q8')->applyFromArray($encabezado_azul);


                $sheet->mergeCells('R7:R8');
                $sheet->setCellValue('R7', 'MONTO DE LISTA DE CORREO');
                $sheet->getStyle('R7:R8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('S8');
                $sheet->setCellValue('S8', 'TOTAL ALMACENAJE DIARIO Bs.');
                $sheet->getStyle('S8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('T8');
                $sheet->setCellValue('T8', 'PRESENTACIÓN A LA ADUANA, COBRO EN DESTINO  ');
                $sheet->getStyle('T8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('U8');
                $sheet->setCellValue('U8', 'AVISO DE LLEGADA Bs.');
                $sheet->getStyle('U8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('V8');
                $sheet->setCellValue('V8', 'ENTREGA MAS DE 0,500 Gr. Bs.');
                $sheet->getStyle('V8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('W8');
                $sheet->setCellValue('W8', 'ENTREGA MÁS DE 2 KG');
                $sheet->getStyle('W8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('X7:X8');
                $sheet->setCellValue('X7', "PETICIÓN DE DEVOLUCIÓN \n O MODIFICACIÓN");
                $sheet->getStyle('X7:X8')->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension('X')->setAutoSize(true);
                $sheet->getStyle('X7:X8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('Y7:Y8');
                $sheet->setCellValue('Y7', 'SUB TOTAL Bs.');
                $sheet->getColumnDimension('Y')->setAutoSize(true);
                $sheet->getStyle('Y7:Y8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('Z8');
                $sheet->setCellValue('Z8', '16%');
                $sheet->getStyle('Z8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('AA7:AA8');
                $sheet->setCellValue('AA7', 'TOTAL A PAGAR');
                $sheet->getColumnDimension('AA')->setAutoSize(true);
                $sheet->getStyle('AA7:AA8')->applyFromArray($encabezado_azul);

                $sheet->mergeCells('AB7:AB8');
                $sheet->setCellValue('AB7', 'OBSERVACIONES');
                $sheet->getColumnDimension('AB')->setAutoSize(true);
                $sheet->getStyle('AB7:AB8')->applyFromArray($encabezado_azul);

                foreach(range('A', 'W') as $col){
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                
                $contador = 1;
                $row = 9;
                foreach($this->envios as $envio){

                    $tipo_cobro = $envio->registro_entrega?->tipo_cobro_extra;
                    $monto_cobro = $envio->registro_entrega?->monto_cobro_extra;

                    if($tipo_cobro === 'nacional 2kg'){
                        $sheet->setCellValue("V{$row}", 0);
                        $sheet->setCellValue("W{$row}", $monto_cobro);
                    }elseif($tipo_cobro === 'internacional 500gr'){
                        $sheet->setCellValue("V{$row}", $monto_cobro);
                        $sheet->setCellValue("W{$row}", 0);
                    }elseif($tipo_cobro == null){
                        $sheet->setCellValue("V{$row}", 0);
                        $sheet->setCellValue("W{$row}", 0);
                    }
                    

                    $sheet->setCellValue("A{$row}", $contador);
                    $sheet->setCellValue("C{$row}", $envio->Entrada);
                    $sheet->setCellValue("D{$row}", $envio->codigo);
                    $sheet->setCellValue("E{$row}", $envio->envio->oficinas->nombre);
                    $sheet->setCellValue("F{$row}", $envio->oficina->nombre);
                    $sheet->setCellValue("G{$row}", $envio->oficina->estado->nombre);
                    $sheet->setCellValue("H{$row}", $envio->aviso->count());
                    $sheet->setCellValue("I{$row}", $envio->Salida);
                    $sheet->setCellValue("J{$row}", $envio->envio_internacional?->lista_correo ? 'Si':'No');
                    $sheet->setCellValue("K{$row}", $envio->Entrada);
                    $sheet->setCellValue("L{$row}", $envio->Salida);
                    $sheet->setCellValue("M{$row}", $envio->registro_entrega?->dias_almacenaje ?? 0);
                    $sheet->setCellValue("N{$row}", $envio->envio->peso .' '. 'GR');
                    $sheet->setCellValue("R{$row}", $envio->registro_entrega->lista_correo ?? 0);
                    $sheet->setCellValue("S{$row}", $envio->registro_entrega->coste_almacenaje ?? 0);
                    $sheet->setCellValue("U{$row}", $envio->registro_entrega->coste_aviso ?? 0);
                    
                    $sheet->setCellValue("X{$row}", 0);
                    $sheet->setCellValue("Y{$row}", bcdiv($envio->registro_entrega?->costo_total * 0.84, 1, 2) ?? 0);
                    $sheet->setCellValue("Z{$row}", bcdiv($envio->registro_entrega?->costo_total * 0.16, 1, 2) ?? 0);
                    $sheet->setCellValue("AA{$row}",$envio->registro_entrega?->costo_total ?? 0);

                    $sheet->getStyle("A{$row}:AA{$row}")->getAlignment()->setHorizontal('center');
                    $row++;
                    $contador++;
                }
            }

        ];
    }

    public function title(): string
    {
        return 'Entregas';
    }
}
