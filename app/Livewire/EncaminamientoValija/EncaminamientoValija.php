<?php

namespace App\Livewire\EncaminamientoValija;

use App\Models\Saca;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EncaminamientoValija extends Component
{
    public $search;
    public $sacas;

    public function getPathing()
    {
        $this->validate([
            'search' => 'required',
        ], [
            'search.required' => 'Este campo es obligatorio.',
        ]);

        $this->sacas = Saca::with([
                'oficinaOrigen',
                'oficinaDestino',
                'usuario',
                'envios.envio_encaminamientos.envio_estatus',
                // Historial de encaminamiento de la propia valija (creada/transito/recibida).
                // Sirve tambien para la valija de peso, que no tiene envios.
                'encaminamientos' => fn($q) => $q->orderBy('saca_encaminamiento_id'),
                'encaminamientos.oficina',
                'encaminamientos.sacaEstatus',
                'encaminamientos.usuario',
            ])
            ->where('codigo_saca', $this->search)
            ->get();

        if ($this->sacas->isEmpty()) {
            toastr()->error('No existe ninguna valija con este código.');
            $this->sacas = null;
        }
    }

    public function render()
    {
        return view('livewire.encaminamiento-valija.encaminamiento-valija');
    }
}
