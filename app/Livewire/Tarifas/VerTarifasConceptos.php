<?php

namespace App\Livewire\Tarifas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\TarifaNacionalConcepto;

class VerTarifasConceptos extends Component
{

    #[Layout('layouts.app')] 
    public function render()
    {
        return view('livewire.tarifas.ver-tarifas-conceptos');
    }
}

