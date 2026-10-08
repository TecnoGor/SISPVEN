<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class EnviosExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents, ShouldAutoSize
{
    protected $envios;
    protected $usuario;
    protected $nombreOficina;
    protected $titulo;
    protected $labelFecha;

    public function __construct(Collection $envios, $usuario, $nombreOficina, $titulo)
    {
        $this->envios = $envios;
        $this->usuario = $usuario;
        $this->nombreOficina = $nombreOficina;
        $this->titulo = $titulo;

        $this->labelFecha = str_contains(strtolower($titulo), 'disponible') ? 'Fecha Entrada' : 'Fecha Salida';
    }

    public function collection()
    {
        return $this->envios->map(function ($envio) {
            $fecha = $envio->created_at ?? null;
            $diasAlmacenados = $fecha ? Carbon::parse($fecha)->diffInDays(Carbon::now()) : 'N/A';

            return [
                'Número de Envío' => $envio->envio->codigo_envio ?? 'N/A',
                'Contenido' => $envio->contenido ?? 'N/A',
                'Peso' => $envio->peso ?? 'N/A',
                'Tipo' => $envio->tipo_envio ?? 'N/A',
                $this->labelFecha => $fecha ? Carbon::parse($fecha)->format('Y-m-d') : 'N/A',
                'Días Almacenados' => $diasAlmacenados,
                'Estatus' => $envio->estatus ? 'Por entregar' : 'Entregado',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Número de Envío',
            'Contenido',
            'Peso',
            'Tipo',
            $this->labelFecha,
            'Días Almacenados',
            'Estatus',
        ];
    }

    public function startCell(): string
    {
        return 'A6'; // Deja espacio para el título y metadata
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('A3:G3');
                $sheet->mergeCells('A4:G4');

                $sheet->setCellValue('A1', $this->titulo);
                $sheet->setCellValue('A2', 'Reporte generado por: ' . $this->usuario->name);
                $sheet->setCellValue('A3', 'Oficina: ' . $this->nombreOficina);
                $sheet->setCellValue('A4', 'Fecha de generación: ' . Carbon::now()->format('d/m/Y H:i'));

                $sheet->getStyle('A1:A4')->getFont()->setBold(true);
                $sheet->getStyle('A1:A4')->getFont()->setSize(12);
            }
        ];
    }
}
