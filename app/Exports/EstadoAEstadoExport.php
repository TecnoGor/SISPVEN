<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Saca;
use Carbon\Carbon;

class EstadoAEstadoExport implements FromCollection, WithEvents, WithTitle, WithMapping, WithCustomStartCell
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

    public function collection()
    {
        $desde = Carbon::parse($this->desde)->startOfDay();
        $hasta = Carbon::parse($this->hasta)->endOfDay();

        $query = Saca::with([
            'envios' => function ($q) {
                $q->with(['envio_encaminamientos.envio_estatus', 'envio_encaminamientos.oficina_externa', 'servicio']);
            },
            'oficinaOrigen',
            'oficinaDestino',
            'numeroDespacho',
        ])
            ->whereHas('envios.envio_encaminamientos', function ($q) use ($desde, $hasta) {
                $q->whereHas('oficina_externa', function ($q2) {
                    $q2->where('tipo_oficina_id', 4)
                        ->where('externa', true)
                        ->where('oficina_id', '!=', 218);
                })->whereBetween('created_at', [$desde, $hasta]);
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
        return 'A7';
    }

    public function map($saca): array
    {
        $this->rowNumber++;
        $envios = $saca->envios;

        $entidad_remitente = $saca->oficinaOrigen ? $saca->oficinaOrigen->nombre : '';

        $entidad_receptora = '';
        if ($envios && $envios->count() > 0) {
            foreach ($envios as $envio) {
                $encaminamiento_aliado = $envio->envio_encaminamientos->first(function ($enc) {
                    return $enc->oficina_externa &&
                        $enc->oficina_externa->tipo_oficina_id == 4 &&
                        $enc->oficina_externa->externa == true &&
                        $enc->oficina_externa->oficina_id != 218;
                });

                if ($encaminamiento_aliado) {
                    $entidad_receptora = $encaminamiento_aliado->oficina_externa->nombre;
                    break;
                }
            }
        }

        // Fallback en caso de no encontrarse una aliada (aunque por la consulta debería existir).
        if (empty($entidad_receptora)) {
            $entidad_receptora = $saca->oficinaDestino ? $saca->oficinaDestino->nombre : '';
        }

        $fecha_recepcion = Carbon::parse($saca->created_at)->format('d/m/Y');

        $master_num = $saca->codigo_saca ?? $saca->saca_id;
        $despacho_num = $saca->numeroDespacho ? $saca->numeroDespacho->numero_despacho_id : '';

        $estatus_envio = '';
        $tipo_servicio = '';

        $monto_sin_iva = 0;
        $iva = 0;
        $total = 0;

        if ($envios && $envios->count() > 0) {
            $primer_envio = $envios->first();

            $ultimo_encaminamiento = $primer_envio->envio_encaminamientos->last();
            if ($ultimo_encaminamiento && $ultimo_encaminamiento->envio_estatus) {
                $estatus_envio = $ultimo_encaminamiento->envio_estatus->nombre ?? '';
            }

            if ($primer_envio->servicio) {
                $tipo_servicio = $primer_envio->servicio->nombre ?? '';
            }

            foreach ($envios as $envio) {
                $total += $envio->coste ?? 0;
            }

            $monto_sin_iva = $total / 1.16;
            $iva = $total - $monto_sin_iva;

            $monto_sin_iva = bcdiv($monto_sin_iva, '1', 2);
            $iva = bcdiv($iva, '1', 2);
            $total = bcdiv($total, '1', 2);
        }

        $peso_valija = $saca->peso ?? $envios->sum('peso');
        $cantidad_piezas = $envios->count();

        return [
            $this->rowNumber,                            // N° (A)
            $entidad_remitente,                          // ENTIDAD REMITENTE (B)
            $entidad_receptora,                          // ENTIDAD RECEPTORA (C)
            $fecha_recepcion,                            // FECHA DE RECEPCIÓN EN LA ENTIDAD (D)
            $master_num,                                 // N° DEL MASTER (E)
            $despacho_num,                               // N° DEL DESPACHO (F)
            1,                                           // CANTIDAD DE VALIJAS (G)
            $peso_valija,                                // PESO KG (H)
            $cantidad_piezas,                            // CANTIDAD DE PIEZAS DENTRO DE LA VALIJA (I)
            $estatus_envio,                              // ESTATUS DEL ENVIO (J)
            $tipo_servicio,                              // TIPO DE SERVICIOS (K)
            '',                                          // PIEZAS ENTREGADAS (L)
            '',                                          // PENDIENTE POR ENTREGAR (M)
            '',                                          // FECHA DE ENTREGA FINAL (N)
            '',                                          // N° DE FACTURA (O)
            '',                                          // N° DE TRANSFERENCIA/DEPOSITO (P)
            $monto_sin_iva,                              // MONTO SIN I.V.A (Q)
            $iva,                                        // I.V.A 16% (R)
            $total,                                      // TOTAL (S)
            '',                                          // OBSERVACIONES (T)
        ];
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
                $drawing->setPath(public_path('images/cintillo.jpg'));
                $drawing->setHeight(60);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
                $sheet->getRowDimension(1)->setRowHeight(25);

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

                // FORMATO
                $sheet->mergeCells('A4:C4');
                $sheet->setCellValue('A4', 'FORMATO PASO 2');
                $sheet->getStyle('A4:C4')->applyFromArray($encabezado_purpura);

                // TITULO
                $sheet->mergeCells('D4:T4');
                $sheet->setCellValue('D4', 'MASTER DE ESTADO A ESTADO - NIVEL NACIONAL');
                $sheet->getStyle('D4:T4')->applyFromArray($encabezado_purpura);

                // Opcional: aplicar bordes a los títulos
                $sheet->getStyle('A4:T4')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Títulos simples
                $titulos = [
                    'A' => 'N°',
                    'B' => 'ENTIDAD REMITENTE',
                    'C' => 'ENTIDAD RECEPTORA',
                    'D' => 'FECHA DE RECEPCIÓN EN LA ENTIDAD',
                    'E' => 'N° DEL MASTER',
                    'F' => 'N° DEL DESPACHO',
                    'G' => 'CANTIDAD DE VALIJAS',
                    'H' => 'PESO KG',
                    'I' => 'CANTIDAD DE PIEZAS DENTRO DE LA VALIJA',
                    'J' => 'ESTATUS DEL ENVIO',
                    'K' => 'TIPO DE SERVICIOS',
                    'L' => 'PIEZAS ENTREGADAS',
                    'M' => 'PENDIENTE POR ENTREGAR',
                    'N' => 'FECHA DE ENTREGA FINAL',
                    'O' => 'N° DE FACTURA',
                    'P' => 'N° DE TRANSFERENCIA/DEPOSITO',
                    'Q' => 'MONTO SIN I.V.A',
                    'R' => 'I.V.A 16%',
                    'S' => 'TOTAL',
                    'T' => 'OBSERVACIONES'
                ];

                foreach ($titulos as $col => $title) {
                    $sheet->mergeCells("{$col}5:{$col}6");
                    $sheet->setCellValue("{$col}5", $title);
                }

                $sheet->getStyle('A5:T6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                foreach (range('A', 'T') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(6)->setRowHeight(25);
            },
        ];
    }

    public function title(): string
    {
        return 'master_de_estado';
    }
}
