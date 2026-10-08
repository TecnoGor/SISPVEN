<?php

namespace App\Exports;

use App\Models\RegistroEntrega;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class EntregasInternacionalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $desde;
    protected $hasta;

    public function __construct($desde, $hasta)
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
    }

    public function collection()
    {
        return RegistroEntrega::where('nacional?', false)
            ->whereBetween('created_at', [Carbon::parse($this->desde)->startOfDay(), Carbon::parse($this->hasta)->endOfDay()])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Código Envío',
            'Cédula Remitente',
            'Nombre Remitente',
            'Costo Total (Bs)',
            'Costo Aviso (Bs)',
            'Costo Almacenaje (Bs)',
            'Fecha de Registro',
        ];
    }

    public function map($entrega): array
    {
        return [
            $entrega->codigo_envio,
            $entrega->cedula_remitente ?? 'N/A',
            $entrega->nombre_remitente ?? 'N/A',
            number_format($entrega->costo_total, 2) . ' Bs',
            number_format($entrega->coste_aviso, 2) . ' Bs',
            number_format($entrega->coste_almacenaje, 2) . ' Bs',
            $entrega->created_at->format('d-m-Y H:i'),
        ];
    }
}
