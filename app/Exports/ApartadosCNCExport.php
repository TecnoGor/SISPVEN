<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class ApartadosCNCExport implements ShouldAutoSize, WithEvents
{
    public $apartados;
    public $dias_faltantes;
    public $filtros;

    public function __construct($apartados, $dias_faltantes, $filtros = [])
    {
        $this->apartados = $apartados;
        $this->dias_faltantes = $dias_faltantes;
        $this->filtros = $filtros;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Cintillo institucional
                $sheet->mergeCells('A1:F1');
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

                // Estilo común para encabezados azules
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

                // Título del reporte (ocupando dos filas)
                $sheet->mergeCells('A4:F5');
                $sheet->setCellValue('A4', 'REPORTE DE APARTADOS POSTALES CNC');
                $sheet->getStyle('A4:F5')->applyFromArray($encabezado_azul);

                // Información de la oficina (ocupando dos filas)
                $fila_oficina = 6;
                $oficina_texto = !empty($this->filtros['oficina_nombre']) ? $this->filtros['oficina_nombre'] : 'Todas las oficinas';
                $sheet->mergeCells('A' . $fila_oficina . ':F' . ($fila_oficina + 1));
                $sheet->setCellValue('A' . $fila_oficina, 'OFICINA: ' . $oficina_texto);
                $sheet->getStyle('A' . $fila_oficina . ':F' . ($fila_oficina + 1))->applyFromArray($encabezado_azul);

                // Fecha de generación (ocupando dos filas)
                $fila_fecha = $fila_oficina + 2;
                $sheet->mergeCells('A' . $fila_fecha . ':F' . ($fila_fecha + 1));
                $sheet->setCellValue('A' . $fila_fecha, 'GENERADO EL: ' . Carbon::now()->format('d/m/Y H:i:s'));
                $sheet->getStyle('A' . $fila_fecha . ':F' . ($fila_fecha + 1))->applyFromArray($encabezado_azul);

                // Información de filtros aplicados (si hay filtros adicionales)
                $fila_filtros = $fila_fecha + 2;
                if (!empty($this->filtros)) {
                    $filtros_texto = [];
                    
                    if (!empty($this->filtros['search'])) {
                        $filtros_texto[] = "Búsqueda: " . $this->filtros['search'];
                    }
                    
                    if (!empty($this->filtros['desde'])) {
                        $filtros_texto[] = "Desde: " . Carbon::parse($this->filtros['desde'])->format('d/m/Y');
                    }
                    
                    if (!empty($this->filtros['hasta'])) {
                        $filtros_texto[] = "Hasta: " . Carbon::parse($this->filtros['hasta'])->format('d/m/Y');
                    }
                    
                    if (!empty($filtros_texto)) {
                        $sheet->mergeCells('A' . $fila_filtros . ':F' . $fila_filtros);
                        $sheet->setCellValue('A' . $fila_filtros, 'FILTROS APLICADOS: ' . implode(' | ', $filtros_texto));
                        $sheet->getStyle('A' . $fila_filtros)->applyFromArray([
                            'font' => ['italic' => true, 'size' => 10],
                            'alignment' => ['horizontal' => 'left'],
                        ]);
                        $fila_filtros++;
                    }
                }

                // Encabezados de columnas (ocupando dos filas)
                $fila_encabezados = $fila_filtros + 1;
                $encabezados = [
                    'A' => 'CÓDIGO',
                    'B' => 'CONDICIÓN',
                    'C' => 'NRO DOCUMENTO',
                    'D' => 'CLIENTE',
                    'E' => 'DÍAS RESTANTES',
                    'F' => 'ESTATUS'
                ];

                foreach ($encabezados as $col => $titulo) {
                    $sheet->mergeCells($col . $fila_encabezados . ':' . $col . ($fila_encabezados + 1));
                    $sheet->setCellValue($col . $fila_encabezados, $titulo);
                    $sheet->getStyle($col . $fila_encabezados . ':' . $col . ($fila_encabezados + 1))->applyFromArray($encabezado_azul);
                }

                // Datos (empiezan después de los encabezados de dos filas)
                $fila_datos = $fila_encabezados + 2;
                
                foreach ($this->apartados as $apartado) {
                    $registro = $apartado->registro_apartado()->where('activo', true)->latest()->first();
                    
                    $sheet->setCellValue("A{$fila_datos}", $apartado->apartado ?: 'No disponible');
                    $sheet->setCellValue("B{$fila_datos}", $apartado->operativo ? 'Operativo' : 'Inoperativo');
                    $sheet->setCellValue("C{$fila_datos}", $registro ? $registro->tipo_documento . '-' . $registro->documento : 'No existe cliente actual');
                    $sheet->setCellValue("D{$fila_datos}", $registro ? $registro->nombre . ' ' . $registro->apellido : 'No existe cliente actual');
                    $sheet->setCellValue("E{$fila_datos}", isset($this->dias_faltantes[$apartado->codigo_apartado_id]) ? $this->dias_faltantes[$apartado->codigo_apartado_id] : '--');
                    $sheet->setCellValue("F{$fila_datos}", $apartado->activo ? 'Activo' : 'Inactivo');
                    
                    $fila_datos++;
                }

                // Aplicar bordes a los datos
                if ($fila_datos > $fila_encabezados + 2) {
                    $dataRange = "A{$fila_encabezados}:F" . ($fila_datos - 1);
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => 'thin'],
                        ],
                    ]);
                }

                // Autoajuste de columnas
                foreach (range('A', 'F') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Altura de filas de encabezados
                $sheet->getRowDimension($fila_encabezados)->setRowHeight(25);
                $sheet->getRowDimension($fila_encabezados + 1)->setRowHeight(25);
            },
        ];
    }
}
