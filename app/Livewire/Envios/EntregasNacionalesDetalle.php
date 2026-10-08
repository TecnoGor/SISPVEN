<?php

namespace App\Livewire\Envios;

use App\Models\RegistroEntrega;
use App\Models\Servicio;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class EntregasNacionalesDetalle extends Component
{
    public $hasta;
    public $desde;
    public $servicio_id;

    public function render()
    {
        $usuario = auth()->user();

    $servicio = Servicio::find($this->servicio_id);

        $fechaInicio = $this->desde ? Carbon::parse($this->desde)->startOfDay() : Carbon::today()->startOfDay();
        $fechaFin = $this->hasta ? Carbon::parse($this->hasta)->endOfDay() : Carbon::today()->endOfDay();
        
        $entregas = RegistroEntrega::where('usuario_id', $usuario->id)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->when($this->servicio_id, function ($query) {
                return $query->where('servicio_id', $this->servicio_id);
            })
            ->get();

            $entregaIds = $entregas->pluck('registro_entrega_id'); // Obtiene los IDs de los registros de entrega
            $pagos = DB::table('facturacion_destinatario')
            ->whereIn('registro_entrega_id', $entregaIds) // Filtra por los registros de entrega seleccionados
            ->join('tipos_pagos', 'facturacion_destinatario.tipo_pago_id', '=', 'tipos_pagos.tipo_pago_id') // Unir con tipoPago
            ->select('facturacion_destinatario.tipo_pago_id', 'tipos_pagos.nombre as tipo_pago_nombre', DB::raw('SUM(facturacion_destinatario.monto) as total_monto')) // Obtener nombre y sumar montos
            ->groupBy('facturacion_destinatario.tipo_pago_id', 'tipos_pagos.nombre') // Agrupar por tipo de pago y su nombre
            ->get();
        
        return view('livewire.envios.entregas-nacionales-detalle',
        [
            'entregas' => $entregas,
            'servicio' => $servicio,
            'pagos' => $pagos,
        ]);
    }
}
