<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class TelegramasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithCustomStartCell
{
    protected $telegramas;
    protected $seccion;

    public function __construct($telegramas, $seccion)
    {
        $this->telegramas = $telegramas;
        $this->seccion = $seccion;
    }

    public function collection()
    {
        return $this->telegramas;
    }

    public function headings(): array
    {
        $headings = [
            'Oficina Origen',
            'Remitente',
            'Oficina Destino',
            'Destinatario',
            'GIT'
        ];

        if ($this->seccion == 2) {
            $headings[] = 'Aprobación';
        } elseif ($this->seccion == 1) {
            $headings[] = 'Estatus';
            $headings[] = 'Cant. Avisos';
        }

        return $headings;
    }


    public function map($telegrama): array
    {
        $row = [
            $telegrama->oficinas->nombre,
            $telegrama->nombre_rem . ' ' . $telegrama->apellido_rem,
            $telegrama->oficinas_destino->nombre,
            $telegrama->nombre_dest . ' ' . $telegrama->apellido_dest,
            $telegrama->codigo_envio
        ];

        if ($this->seccion == 2) {
            $recibido = optional($telegrama->telegrama_recibido->first())->recibido;
            $row[] = $recibido ? 'Recibido' : 'En espera';
        } elseif ($this->seccion == 1) {
            $estatus = optional($telegrama->telegrama_recibido->first())->recibido;
            $row[] = $estatus ? 'Aprobado' : 'Sin Aprobar';
            $count = optional($telegrama->avisos_telegrama)->count();
            $row[] = $count === 0 ? '0' : $count;
        }

        return $row;
    }

    public function startCell(): string
    {
        return 'A5'; // Headings en fila 4-5, datos desde la fila 6
    }


    public function styles(Worksheet $sheet)
    {
        $colCount = count($this->headings());
        $lastColumn = Coordinate::stringFromColumnIndex($colCount);

        return [
            // Estiliza los encabezados en la fila 4
            4 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF000080'],
                ],
            ],
        ];
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $colCount = count($this->headings());
                $lastColumn = Coordinate::stringFromColumnIndex($colCount);

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
                $sheet->getStyle("A1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['argb' => 'FF222222']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Autoajuste de ancho
                foreach (range('A', 'G') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $highestRow = $sheet->getHighestRow();

                // Bordes finos para todo el bloque
                $sheet->getStyle("A4:G{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF333333'],
                        ],
                    ],
                ]);

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

                $simpleTitles = [
                    'A' => 'Oficina Origen',
                    'B' => 'Remitente',
                    'C' => 'Oficina Destino',
                    'D' => 'Destinatario',
                    'E' => 'GIT',
                    'F' => 'Estatus',
                    'G' => 'Cant. Avisos',
                ];

                foreach ($simpleTitles as $col => $title) {
                    $sheet->mergeCells("{$col}4:{$col}5");
                    $sheet->setCellValue("{$col}4", $title);
                    $sheet->getStyle("{$col}4:{$col}5")->applyFromArray($encabezado_azul);
                }

                 // Estilos para todo el rango de encabezados
                $sheet->getStyle('A4:G4')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '002F6C'],
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                // Alternancia de color de fondo (estilo zebra)
                for ($row = 5; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF7F7F7'], // Gris claro
                            ],
                        ]);
                    }
                }
            },
        ];
    }
}
