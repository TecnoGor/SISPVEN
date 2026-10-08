<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Saca;
use App\Models\EnvioEncaminamiento;
use Carbon\Carbon;


class CssEstadoExport implements FromCollection, WithEvents, WithTitle, WithMapping, WithCustomStartCell
{
    protected $desde;
    protected $hasta;
    protected $estado;
    protected $rowNumber = 0;

    public function __construct($desde, $hasta, $estado)
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->estado = $estado;
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
                $sheet->getRowDimension(7)->setRowHeight(40);


                // SUBENCABEZADO DE INFORMACIÓN

                $sheet->mergeCells('A5');
                $sheet->setCellValue('A5', 'MRW');

                $sheet->mergeCells('A6:B6');
                $sheet->setCellValue('A6', 'ESTADO:');

                $sheet->getStyle('A6:B6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Borde y valor para la celda de respuesta a ESTADO:
                $sheet->mergeCells('C6:D6');
                $sheet->getStyle('C6:D6')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                $estadoNombre = 'Nivel Nacional';
                if ($this->estado) {
                    $estadoObj = \App\Models\Estado::find($this->estado);
                    if ($estadoObj) {
                        $estadoNombre = $estadoObj->nombre;
                    }
                }

                $sheet->setCellValue('C6', $estadoNombre);
                $sheet->getStyle('C6')->getFont()->setBold(true);
                $sheet->getStyle('C6')->getAlignment()->setHorizontal('center')->setVertical('center');

                $sheet->mergeCells('A7');
                $sheet->setCellValue('A7', 'N°');

                $sheet->mergeCells('B7');
                $sheet->setCellValue('B7', 'N° De Master');

                $sheet->mergeCells('C7');
                $sheet->setCellValue('C7', 'N° De despacho');

                $sheet->mergeCells('D7');
                $sheet->setCellValue('D7', "Fecha de Envio a\n Plataforma MRW");

                $sheet->mergeCells('E7');
                $sheet->setCellValue('E7', 'Cantidad de Valijas');

                $sheet->mergeCells('F7');
                $sheet->setCellValue('F7', 'Peso gr');

                $sheet->mergeCells('G7');
                $sheet->setCellValue('G7', "Fecha de Llegada a\n Plataforma MRW DESTINO");

                $sheet->mergeCells('H7');
                $sheet->setCellValue('H7', "Fecha de Retiro\n por  Ipostel");

                $sheet->mergeCells('I7');
                $sheet->setCellValue('I7', "Cantidad de Piezas\n dentro de la valija");

                $sheet->mergeCells('J7');
                $sheet->setCellValue('J7', 'Peso Por Ipostel');

                $sheet->mergeCells('K7');
                $sheet->setCellValue('K7', 'Fecha de llegada a OPT');

                $sheet->mergeCells('L7');
                $sheet->setCellValue('L7', 'Estatus del paquete');

                $sheet->mergeCells('M7');
                $sheet->setCellValue('M7', 'Tipo de servicio');

                $sheet->mergeCells('N7');
                $sheet->setCellValue('N7', "Piezas Pendientes\n Por Entregar");

                $sheet->mergeCells('O7');
                $sheet->setCellValue('O7', 'Fecha de Entrega Final');

                $sheet->mergeCells('P7');
                $sheet->setCellValue('P7', 'N° de Factura');

                $sheet->mergeCells('Q7');
                $sheet->setCellValue('Q7', "N° de transferencia\n / Deposito");

                $sheet->mergeCells('R7');
                $sheet->setCellValue('R7', 'Monto Sin I.V.A');

                $sheet->mergeCells('S7');
                $sheet->setCellValue('S7', 'I.V.A 16%');

                $sheet->mergeCells('T7');
                $sheet->setCellValue('T7', 'TOTAL');

                $sheet->mergeCells('U7');
                $sheet->setCellValue('U7', "Fecha de Carga\n en el IPS");

                // Estilos para todo el rango de encabezados fusionados entre A6 y E7
                $sheet->getStyle('A7:U7')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                foreach (range('A', 'U') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                foreach (range('A', 'U') as $col) {
                    $sheet->getStyle($col . '7')->getAlignment()->setWrapText(true);
                }
            },
        ];
    }

    public function title(): string
    {
        return 'css_estado';
    }

    public function collection()
    {
        $desde = Carbon::parse($this->desde)->startOfDay();
        $hasta = Carbon::parse($this->hasta)->endOfDay();

        $query = Saca::with([
            'envios' => function ($q) {
                $q->with('envio_encaminamientos.envio_estatus', 'servicio');
            },
            'oficinaOrigen',
            'oficinaDestino',
            'numeroDespacho',
        ])
            ->whereHas('envios.envio_encaminamientos', function ($query) use ($desde, $hasta) {
                $query->where('oficina_externa_id', 218) // MRW
                    ->whereBetween('created_at', [$desde, $hasta]);
            });

        if ($this->estado) {
            $query->whereHas('oficinaOrigen', function ($q) {
                $q->where('estado_id', $this->estado);
            });
        }

        return $query->get();
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function map($saca): array
    {
        $this->rowNumber++;
        $envios = $saca->envios;

        $master_num = $saca->codigo_saca ?? $saca->saca_id;
        $despacho_num = $saca->numeroDespacho ? $saca->numeroDespacho->numero_despacho_id : '';

        $fecha_envio_mrw = '';
        $fecha_carga_ips = Carbon::parse($saca->created_at)->format('d/m/Y');

        $estatus_paquete = '';
        $tipo_servicio = '';

        $monto_sin_iva = 0;
        $iva = 0;
        $total = 0;

        if ($envios && $envios->count() > 0) {
            $primer_envio = $envios->first();

            $encaminamiento_mrw = $primer_envio->envio_encaminamientos->where('oficina_externa_id', 218)->first();
            if ($encaminamiento_mrw) {
                $fecha_envio_mrw = Carbon::parse($encaminamiento_mrw->created_at)->format('d/m/Y');
            }

            // Usamos el último encaminamiento para sacar el estatus de paquete
            $ultimo_encaminamiento = $primer_envio->envio_encaminamientos->last();
            if ($ultimo_encaminamiento && $ultimo_encaminamiento->envio_estatus) {
                $estatus_paquete = $ultimo_encaminamiento->envio_estatus->nombre ?? '';
            }

            if ($primer_envio->servicio) {
                $tipo_servicio = $primer_envio->servicio->nombre ?? '';
            }

            foreach ($envios as $envio) {
                // El campo 'coste' en la tabla 'envios' ya incluye el total facturado
                $total += $envio->coste ?? 0;
            }

            // A partir del TOTAL acumulado, desglosamos cuánto fue de Base (Monto sin I.V.A) y cuánto de IVA
            // MontoBase = Total / 1.16
            // IVA = Total - MontoBase
            $monto_sin_iva = $total / 1.16;
            $iva = $total - $monto_sin_iva;

            // Mantener exactamente 2 decimales truncando y en formato flotante
            $monto_sin_iva = bcdiv($monto_sin_iva, '1', 2);
            $iva = bcdiv($iva, '1', 2);
            $total = bcdiv($total, '1', 2);
        }

        $peso_valija = $envios->sum('peso');
        $peso_ipostel = $saca->peso;
        $cantidad_piezas = $envios->count();

        return [
            $this->rowNumber,                            // N°
            $master_num,                                 // N° De Master
            $despacho_num,                               // N° De despacho
            $fecha_envio_mrw,                            // Fecha de Envio a Plataforma MRW
            1,                                           // Cantidad de Valijas
            $peso_valija,                                // Peso
            '',                                          // Fecha de Llegada a Plataforma MRW DESTINO
            '',                                          // Fecha de Retiro por Ipostel
            $cantidad_piezas,                            // Cantidad de Piezas dentro de la valija
            $peso_ipostel,                               // Peso Por Ipostel
            '',                                          // Fecha de llegada a OPT
            $estatus_paquete,                            // Estatus del paquete
            $tipo_servicio,                              // Tipo de servicio
            '',                                          // Piezas Pendientes Por Entregar
            '',                                          // Fecha de Entrega Final
            '',                                          // N° de Factura
            '',                                          // N° de transferencia / Deposito
            $monto_sin_iva,                              // Monto Sin I.V.A
            $iva,                                        // I.V.A 16%
            $total,                                      // TOTAL
            $fecha_carga_ips,                            // Fecha de Carga en el IPS
        ];
    }
}
