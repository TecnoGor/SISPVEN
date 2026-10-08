<?php

namespace App\Livewire\Manifiestos;

use Livewire\Component;
use App\Models\NumeroDespachoOficina;
use App\Models\Saca;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.app')]
class GuiaDespacho extends Component
{
    public $despachos;
    public $fechaDesde;
    public $fechaHasta;

    public function mount()
    {
        // Por defecto, el rango se ubica en el dia de hoy (desde = hasta = hoy).
        $this->fechaDesde = today()->toDateString();
        $this->fechaHasta = today()->toDateString();
        $this->cargarDespachos();
    }

    public function updatedFechaDesde()
    {
        $this->cargarDespachos();
    }

    public function updatedFechaHasta()
    {
        $this->cargarDespachos();
    }

    /**
     * La Guia de Despacho ahora se guia por el DESPACHO (numeros_despacho_oficina),
     * no por el manifiesto. Cada despacho es el correlativo de un par (origen, destino)
     * y agrupa todas las valijas creadas para ese par hasta que se le da salida.
     *
     * Solo se listan los despachos CREADOS en la oficina del usuario (oficina_id),
     * tanto activos como ya despachados: una oficina que solo recibio un despacho
     * ajeno no puede imprimir su guia. Se filtra ademas por la fecha seleccionada.
     */
    public function cargarDespachos()
    {
        $usuario = auth()->user();

        // Rango de fechas [desde, hasta] inclusivo. Si el usuario invierte el orden,
        // se normaliza para que la consulta nunca quede vacia por el orden.
        $desde = Carbon::parse($this->fechaDesde)->startOfDay();
        $hasta = Carbon::parse($this->fechaHasta)->endOfDay();
        if ($desde->gt($hasta)) {
            [$desde, $hasta] = [$hasta->startOfDay(), $desde->endOfDay()];
        }

        $despachos = NumeroDespachoOficina::with('oficinaDestino')
            ->where('oficina_id', $usuario->oficina_id)
            ->whereBetween('created_at', [$desde, $hasta])
            // Un despacho sin contenido no tiene guia que mostrar: su documento
            // saldria vacio. Se comprueban las dos fuentes porque un despacho
            // puede llevar solo envios al descubierto, sin ninguna valija.
            ->where(function ($q) {
                $q->whereHas('sacas')
                    ->orWhereHas('enviosDescubiertos');
            })
            ->orderByDesc('numero_despacho')
            ->get();

        // Conteo de valijas por despacho para mostrarlo en la tarjeta. Una sola
        // consulta agrupada en vez de N consultas dentro del loop de la vista.
        $conteos = Saca::whereIn('numero_despacho_id', $despachos->pluck('numero_despacho_id'))
            ->selectRaw('numero_despacho_id, COUNT(*) as total')
            ->groupBy('numero_despacho_id')
            ->pluck('total', 'numero_despacho_id');

        $despachos->each(function ($despacho) use ($conteos) {
            $despacho->cantidad_valijas = $conteos[$despacho->numero_despacho_id] ?? 0;
        });

        $this->despachos = $despachos;
    }

    public function render()
    {
        return view('livewire.manifiestos.guia-despacho', [
            'despachos' => $this->despachos,
        ]);
    }
}