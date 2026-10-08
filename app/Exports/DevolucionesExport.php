<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Envio;



class DevolucionesExport implements WithEvents, WithTitle
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

                // SUBENCABEZADO DE INFORMACIÓN

                $sheet->mergeCells('A5');
                $sheet->setCellValue('A5', 'N°');

                $sheet->mergeCells('B5');
                $sheet->setCellValue('B5', 'FECHA DE LLEGADA A LA OFICINA');

                $sheet->mergeCells('C5');
                $sheet->setCellValue('C5', 'NRO DEL ENVIO');

                $sheet->mergeCells('D5');
                $sheet->setCellValue('D5', 'REMITENTE');

                $sheet->mergeCells('E5');
                $sheet->setCellValue('E5', 'TELÉFONO REMITENTE');

                $sheet->mergeCells('F5');
                $sheet->setCellValue('F5', 'DIRECCIÓN REMITENTE');

                $sheet->mergeCells('G5');
                $sheet->setCellValue('G5', 'DESTINATARIO');

                $sheet->mergeCells('H5');
                $sheet->setCellValue('H5', 'NRO DE TELEFONO DEL DESTINATARIO');

                $sheet->mergeCells('I5');
                $sheet->setCellValue('I5', 'DIRECCIÓN DEL DESTINATARIO');

                $sheet->mergeCells('J5');
                $sheet->setCellValue('J5', 'TIPO DE SERVICIO');

                $sheet->mergeCells('K5');
                $sheet->setCellValue('K5', 'PESO');

                $sheet->mergeCells('L5');
                $sheet->setCellValue('L5', 'NRO DE DESPACHO DE DEVOLUCION');

                $sheet->mergeCells('M5');
                $sheet->setCellValue('M5', 'PRECINTO DE DEVOLUCION');

                $sheet->mergeCells('N5');
                $sheet->setCellValue('N5', 'FECHA OREJETA');

                $sheet->mergeCells('O5');
                $sheet->setCellValue('O5', 'ESTADO');

                $sheet->mergeCells('P5');
                $sheet->setCellValue('P5', 'ULTIMO EVENTO');

                $sheet->mergeCells('Q5');
                $sheet->setCellValue('Q5', 'FECHA UTIMO EVENTO');

                $sheet->mergeCells('R5');
                $sheet->setCellValue('R5', 'UBICACIÓN IPS');

                $sheet->mergeCells('S5');
                $sheet->setCellValue('S5', 'ULTIMO USUARIO');

                $sheet->mergeCells('T5');
                $sheet->setCellValue('T5', 'SIGUIENTE OFICINA');

                $sheet->mergeCells('U5');
                $sheet->setCellValue('U5', 'CONDICIÓN DEL ENVIO');

                $sheet->mergeCells('V5');
                $sheet->setCellValue('V5', 'MOTIVO DE LA DEVOLUCIÓN');

                $sheet->mergeCells('W5');
                $sheet->setCellValue('W5', 'ESTATUS DEL ENVIO');

                // Estilos para todo el rango de encabezados fusionados
                $sheet->getStyle('A5:W5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                foreach (range('A', 'W') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $row = 6;
                foreach ($this->envios as $index => $envio) {

                    $ultimo_encaminamiento = $envio->envio->envio_encaminamientos
                        ->sortByDesc('created_at')->first();
                
                    $sheet->setCellValue("A{$row}", $index + 1);
                    $sheet->setCellValue("B{$row}", optional($envio->created_at)->format('d/m/Y'));
                    $sheet->setCellValue("C{$row}", $envio->envio->codigo_envio);
                    $sheet->setCellValue("D{$row}", $envio->envio->nombre_rem.' '.$envio->envio->apellido_rem);
                    $sheet->setCellValue("E{$row}", $envio->envio->telefono_rem);
                    $sheet->setCellValue("F{$row}", $envio->envio->direccion_rem);
                    $sheet->setCellValue("G{$row}", $envio->envio->nombre_dest.' '.$envio->envio->apellido_dest);
                    $sheet->setCellValue("H{$row}", $envio->envio->tlf_dest);
                    $sheet->setCellValue("I{$row}", $envio->envio->direccion_dest);
                    $sheet->setCellValue("J{$row}", $envio->envio->servicio?->nombre ?? 'N/A');
                    $sheet->setCellValue("K{$row}", $envio->envio->peso . ' Gr');

                    $sheet->setCellValue("L{$row}", $envio->nro_despacho_devolucion ?? 'N/A');
                    $sheet->setCellValue("M{$row}", $envio->precinto_devolucion);
                    $sheet->setCellValue("N{$row}", optional($envio->fecha_orejeta)->format('d/m/Y'));
                    $sheet->setCellValue("O{$row}", $envio->oficina->estado->nombre ?? 'N/A');
                    $sheet->setCellValue("P{$row}", $ultimo_encaminamiento->envio_estatus?->estatus ?? 'Sin evento');
                    $sheet->setCellValue("Q{$row}", $ultimo_encaminamiento->created_at?->format('d/m/Y H:i') ?? 'N/A');
                    $sheet->setCellValue("R{$row}", $envio->ubicacion_ips ?? 'N/A');
                    $sheet->setCellValue("S{$row}", optional($ultimo_encaminamiento->users)->name ?? 'N/A');
                    $sheet->setCellValue("T{$row}", optional($ultimo_encaminamiento->oficinas)->nombre ?? 'N/A');
                    $sheet->setCellValue("U{$row}", $envio->condicion_envio ?? 'N/A');
                    $sheet->setCellValue("V{$row}", $envio->envio->incidencia->first()->detalle);
                    $sheet->setCellValue("P{$row}", $ultimo_encaminamiento->envio_estatus?->estatus ?? 'Sin evento');
                    $row++;
                    $lastRow = $row - 1;
                    $sheet->getStyle("A6:X{$lastRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical'   => 'center',
                        ],
                    ]);
                }
            },
        ];
    }

    public function title(): string
    {
        return 'devoluciones';
    }
}
