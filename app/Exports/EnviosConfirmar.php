<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class EnviosConfirmar implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $envios;

    public function __construct($envios)
    {
        $this->envios = $envios;
    }

    public function collection()
    {
        return $this->envios->map(function ($envio) {
            return [
                'Servicio' => $envio->servicio->nombre ?? 'No asignado',
                'Usuario' => $envio->users->name ?? 'No encontrado',
                'Fecha de creación' => $envio->created_at->format('d-m-Y'),
                'Código de Envío' => $envio->codigo_envio ?? 'No encontrado',
                'Remitente' => $envio->nombre_rem . ' ' . $envio->apellido_rem ?? 'No encontrado',
                'Documento' => $envio->tipo_documento_rem . '-' . $envio->documento_rem ?? 'No encontrado',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Servicio',
            'Usuario',
            'Fecha de creación',
            'Código de Envío',
            'Remitente',
            'Documento',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila de encabezado
            1    => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => 'center'],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF800000'], // Azul oscuro
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Título general del reporte
                $sheet->mergeCells('A1:F1'); // Combinar celdas
                $sheet->setCellValue('A1', 'Reporte de Envíos Por Confirmar');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Bordes para todas las celdas con datos
                $sheet->getStyle('A2:F100')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
