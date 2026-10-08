<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class EnviosRecibidoExport implements FromQuery, WithEvents, WithMapping, WithCustomStartCell, WithChunkReading
{
    protected $query;
    protected $usuario;
    protected $nivel;
    protected $desde;
    protected $hasta;
    protected $rowNumber = 0;

    /**
     * Recibe la query SIN ejecutar (no una Collection). Maatwebsite la recorre
     * por bloques, de modo que la memoria no crece con el volumen del reporte.
     */
    public function __construct(Builder $query, $usuario, $nivel, $desde, $hasta)
    {
        $this->query = $query;
        $this->usuario = $usuario;
        $this->nivel = $nivel;
        $this->desde = $desde;
        $this->hasta = $hasta;
    }

    public function query()
    {
        return $this->query;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function map($envioAlmacen): array
    {
        $this->rowNumber++;
        $envio = $envioAlmacen->envio;
        $oficinaOrigen = $envio?->oficinaOrigen;
        $estadoOrigen = $oficinaOrigen?->estado;
        $oficinaActual = $envioAlmacen->oficina;
        $estadoActual = $oficinaActual?->estado;
        $estadoDestino = $envio?->estadoDestino;
        $encaminamiento = $envio?->encaminamiento_actual;
        $fechaEntrada = $envioAlmacen->Entrada ?? null;

        $precioSinIva = ($envio && $envio->coste_sin_iva !== null)
            ? number_format((float) $envio->coste_sin_iva, 2, ',', '.')
            : 'N/A';

        // Tasa BCV del día con formato venezolano (12.345,67).
        $tasaBcv = ($envio && $envio->tasa_bs !== null)
            ? number_format((float) $envio->tasa_bs, 2, ',', '.')
            : 'N/A';

        // Estatus Actual y su fecha en columnas separadas.
        if ($encaminamiento && $encaminamiento->envio_estatus) {
            $estatusActual = $encaminamiento->envio_estatus->estatus ?? 'N/A';
            $fechaEstatus = $encaminamiento->created_at
                ? Carbon::parse($encaminamiento->created_at)->format('d/m/Y')
                : 'N/A';
        } else {
            $estatusActual = 'N/A';
            $fechaEstatus = 'N/A';
        }

        // Métodos de pago y referencias (un envío puede tener varios pagos).
        [$metodosPago, $referencias] = $this->resolverPagos($envio);

        return [
            $this->rowNumber,                                                              // N° (A)
            $envio?->codigo_envio ?? 'N/A',                                                // Número de Envío (B)
            $envio?->contenido ?? 'N/A',                                                   // Contenido (C)
            $envio?->peso ?? 'N/A',                                                        // Peso (D)
            $precioSinIva,                                                                 // Precio sin IVA (E)
            $tasaBcv,                                                                      // Tasa BCV del día (F)
            $metodosPago,                                                                  // Método de Pago (G)
            $referencias,                                                                  // Referencia (H)
            $envio?->tipo_envio ? ucfirst($envio->tipo_envio) : 'N/A',                     // Tipo (I)
            $oficinaOrigen ? $oficinaOrigen->nombre : 'N/A',                               // Oficina de Origen (J)
            $estadoOrigen ? $estadoOrigen->nombre : 'N/A',                                 // Estado de Origen (K)
            $oficinaActual ? $oficinaActual->nombre : 'N/A',                               // Oficina Actual (L)
            $estadoActual ? $estadoActual->nombre : 'N/A',                                 // Estado Actual (M)
            $estadoDestino ? $estadoDestino->nombre : 'N/A',                               // Estado de Destino (N)
            $fechaEntrada ? Carbon::parse($fechaEntrada)->format('d/m/Y') : 'N/A',         // Fecha de Registro (O)
            $envio?->servicio ? $envio->servicio->nombre : 'N/A',                          // Servicio (P)
            $envio?->devolucion ? 'En Devolución' : 'Activo',                              // Estatus (Q)
            $estatusActual,                                                                // Estatus Actual (R)
            $fechaEstatus,                                                                 // Fecha de Estatus (S)
        ];
    }

    /**
     * Resuelve los métodos de pago y referencias de un envío a partir de su
     * facturación. Un envío puede tener varios pagos (montos prorrateados),
     * por lo que se concatenan con ' / '. La referencia solo aplica a pagos
     * que la tengan; si ninguno la tiene se devuelve 'N/A'.
     *
     * @return array{0: string, 1: string} [metodosPago, referencias]
     */
    private function resolverPagos($envio): array
    {
        $pagos = $envio?->factura_envio?->facturaciones?->pagos ?? collect();

        if ($pagos->isEmpty()) {
            return ['N/A', 'N/A'];
        }

        $metodos = $pagos
            ->map(fn($pago) => $pago->tipos_pagos->nombre ?? 'N/A')
            ->implode(' / ');

        $referencias = $pagos
            ->map(fn($pago) => $pago->numero_referencia)
            ->filter(fn($ref) => !empty($ref))
            ->implode(' / ');

        return [
            $metodos !== '' ? $metodos : 'N/A',
            $referencias !== '' ? $referencias : 'N/A',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // --- ENCABEZADO INSTITUCIONAL ---
                $sheet->mergeCells('A1:S1');
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

                $sheet->mergeCells('A4:C4');
                $sheet->setCellValue('A4', 'REPORTE PRESIDENCIA');
                $sheet->getStyle('A4:C4')->applyFromArray($encabezado_purpura);

                $sheet->mergeCells('D4:S4');
                $sheet->setCellValue('D4', 'REPORTE DE ENVÍOS RECIBIDOS - ' . $this->nivel);
                $sheet->getStyle('D4:S4')->applyFromArray($encabezado_purpura);

                $sheet->mergeCells('A5:S5');
                $sheet->setCellValue('A5', 'RANGO DE FECHAS: ' . $this->desde . ' al ' . $this->hasta . '  |  GENERADO POR: ' . strtoupper($this->usuario->name));
                $sheet->getStyle('A5:S5')->applyFromArray([
                    'font' => ['bold' => true, 'italic' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'EBEBEB'],
                    ],
                ]);

                $titulos = [
                    'A' => "N°",
                    'B' => "Número de Envío",
                    'C' => "Contenido",
                    'D' => "Peso (gr)",
                    'E' => "Precio sin IVA",
                    'F' => "Tasa BCV del día",
                    'G' => "Método de Pago",
                    'H' => "Referencia",
                    'I' => "Tipo",
                    'J' => "Oficina de Origen",
                    'K' => "Estado de Origen",
                    'L' => "Oficina Actual",
                    'M' => "Estado Actual",
                    'N' => "Estado de Destino",
                    'O' => "Fecha de Registro",
                    'P' => "Servicio",
                    'Q' => "Estatus",
                    'R' => "Estatus Actual",
                    'S' => "Fecha de Estatus",
                ];

                foreach ($titulos as $col => $title) {
                    $sheet->mergeCells("{$col}6:{$col}7");
                    $sheet->setCellValue("{$col}6", $title);
                }

                $sheet->getStyle('A6:S7')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Anchos fijos en vez de setAutoSize(): el autoSize recorre todas
                // las celdas midiendo el texto renderizado al cerrar la hoja, lo
                // que con decenas de miles de filas agota la memoria.
                $anchos = [
                    'A' => 6,   'B' => 20,  'C' => 30,  'D' => 10,  'E' => 15,
                    'F' => 16,  'G' => 20,  'H' => 20,  'I' => 12,  'J' => 28,
                    'K' => 18,  'L' => 28,  'M' => 18,  'N' => 18,  'O' => 16,
                    'P' => 20,  'Q' => 14,  'R' => 22,  'S' => 16,
                ];

                foreach ($anchos as $col => $ancho) {
                    $sheet->getColumnDimension($col)->setWidth($ancho);
                }

                $sheet->getRowDimension(6)->setRowHeight(25);
                $sheet->getRowDimension(7)->setRowHeight(25);
            },
        ];
    }
}