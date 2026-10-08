<?php

namespace App\Livewire\Envios;

use App\Models\FacturacionDestinatario;
use App\Models\RegistroEntrega;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EntregasInternacionalesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

#[Layout('layouts.app')]
class EntregasIntgernacionalesDetalle extends Component
{
    public $desde, $hasta, $cantidad_envios_internacionales, $total_costo_envios;

    public function render()
    {      
        $usuario = auth()->user();

        if (!$this->desde || !$this->hasta) {
            $this->desde = now()->format('Y-m-d');
            $this->hasta = now()->format('Y-m-d');
        }

        $fechaInicio = Carbon::parse($this->desde)->startOfDay();
        $fechaFin = Carbon::parse($this->hasta)->endOfDay();

        $envios_internacionales = RegistroEntrega::where('usuario_id', $usuario->id)
            ->where('nacional?', false)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->get();

        $this->cantidad_envios_internacionales = $envios_internacionales->count();
        $this->total_costo_envios = $envios_internacionales->sum('costo_total');
        $registroEntregaIds = $envios_internacionales->pluck('registro_entrega_id');

        $facturacion_entrega = FacturacionDestinatario::whereIn('registro_entrega_id', $registroEntregaIds)
            ->get()
            ->groupBy('registro_entrega_id')
            ->map(function ($group) {
                $total_pagado = $group->sum('monto');
                return $group->map(function ($item) use ($total_pagado) {
                    $item->total_pagado = $total_pagado;
                    return $item;
                });
            })
            ->flatten();

        return view('livewire.envios.entregas-intgernacionales-detalle', [
            'entregas' => $envios_internacionales,
            'pagos' => $facturacion_entrega,
            'cantidad' => $this->cantidad_envios_internacionales,
            'total_costo_envios' => $this->total_costo_envios,
        ]);
    }

    public function exportarExcel()
    {
        return Excel::download(new EntregasInternacionalesExport($this->desde, $this->hasta), 'entregas_internacionales.xlsx');
    }

    public function exportarPDF()
{
    $usuario = auth()->user();

    $fechaInicio = Carbon::parse($this->desde)->startOfDay();
    $fechaFin = Carbon::parse($this->hasta)->endOfDay();

    $envios_internacionales = RegistroEntrega::where('usuario_id', $usuario->id)
        ->where('nacional?', false)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->get();

    // Calcular los valores directamente en el método
    $cantidad_envios_internacionales = $envios_internacionales->count();
    $total_costo_envios = $envios_internacionales->sum('costo_total');

    $data = [
        'entregas' => $envios_internacionales,
        'cantidad' => $cantidad_envios_internacionales,
        'total_costo_envios' => $total_costo_envios,
        'desde' => $this->desde,
        'hasta' => $this->hasta,
        'usuario' => $usuario,
    ];

    $pdf = Pdf::loadView('pdf.reporte-entregas-internacionales', $data);

    return response()->streamDownload(
        fn () => print($pdf->output()),
        'entregas_internacionales.pdf'
    );
}

}