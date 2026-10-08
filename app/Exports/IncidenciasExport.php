<?php

namespace App\Exports;

use App\Models\EnvioIncidencia;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class IncidenciasExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
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
                'Código Envío' => $envio->envio->codigo_envio ?? 'No encontrado',
                'Tipo de Envío' => $envio->envio->tipo_envio ?? 'No encontrado',
                'Usuario' => $envio->usuario->name ?? 'No encontrado',
                'Peso del Envío' => "{$envio->envio->peso} gr" ?? 'No encontrado',
                'Fecha y Hora' => $envio->created_at->format('d-m-Y H:i') ?? 'No encontrado',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Código Envío',
            'Tipo de Envío',
            'Usuario',
            'Peso del Envío',
            'Fecha y Hora',
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
                $sheet->mergeCells('A1:E1'); // Combinar celdas
                $sheet->setCellValue('A1', 'Reporte de Envios con Incidencias');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Bordes para todas las celdas con datos
                $highestRow = $sheet->getHighestRow(); // Obtener la última fila con datos
                $sheet->getStyle("A2:E{$highestRow}")->applyFromArray([
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





