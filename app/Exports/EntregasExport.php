<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EntregasExport implements FromCollection, WithHeadings, WithStyles
{
    protected $entregas;
    protected $desde;
    protected $hasta;
    protected $nombre;

    public function __construct($entregas, $desde, $hasta, $nombre)
    {
        $this->entregas = $entregas;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->nombre = $nombre;
    }

    

    public function headings(): array
    {
        // Titulos de las columnas para los datos
        return ['Servicio', 'Cantidad', 'Monto'];
    }

    public function styles($sheet)
    {
        // Estilo general para todo el reporte
        $sheet->getStyle('A1:C1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Estilo para las filas de fechas (antes de la tabla)
        $sheet->getStyle('A2:A5')->getFont()->setSize(12);
        $sheet->getStyle('A2:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Estilo para las filas de datos (servicio, cantidad, monto)
        $sheet->getStyle('A7:A' . (count($this->entregas) + 6))->getFont()->setSize(12);
        $sheet->getStyle('A7:C' . (count($this->entregas) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B7:C' . (count($this->entregas) + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Bordes para todo el reporte
        $sheet->getStyle('A1:C' . (count($this->entregas) + 6))
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

        // Fondo para los encabezados de la tabla de datos
        $sheet->getStyle('A1:C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');

        // Estilo para los totales (al final del reporte)
        $sheet->getStyle('A' . (count($this->entregas) + 7) . ':C' . (count($this->entregas) + 8))
              ->getFont()
              ->setBold(true)
              ->setSize(12);
        $sheet->getStyle('A' . (count($this->entregas) + 7) . ':C' . (count($this->entregas) + 8))
              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Estilo para las fechas
        $sheet->getStyle('A2:A5')->getFont()->setBold(true); // Negrita para las fechas
        $sheet->getStyle('A2:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Estilo para las firmas
        $sheet->getStyle('A' . (count($this->entregas) + 9) . ':C' . (count($this->entregas) + 11))
              ->getFont()
              ->setSize(12);
        $sheet->getStyle('A' . (count($this->entregas) + 9) . ':C' . (count($this->entregas) + 11))
              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    public function collection()
    {
        // Datos de las entregas
        $datos = $this->entregas->map(function ($entrega) {
            return [
                $entrega->servicio->nombre,
                $entrega->total_envios,
                $entrega->costo_total . " Bs",
            ];
        });

        // Calcular los totales
        $totalCantidad = $this->entregas->sum('total_envios');
        $totalMonto = $this->entregas->sum('costo_total');

        // Agregar las fechas como una fila extra al principio, antes de los datos
        $fechas = new Collection([
            ['Fecha del reporte', ''],
            ['Desde', $this->desde],
            ['Hasta', $this->hasta],
            ['Oficina', $this->nombre], // Espacio vacío para separar las fechas de los datos
        ]);

        // Agregar los totales al final del reporte
        $totales = new Collection([ 
            ['Total cantidad', $totalCantidad, ''],
            ['Total monto', $totalMonto . " Bs", ''],
        ]);

        // Agregar las filas para las firmas
        $firmas = new Collection([
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
        ]);

        // Unir todas las secciones (fechas, datos, totales, y firmas)
        return $fechas->merge($datos)->merge($totales)->merge($firmas);
    }
}
