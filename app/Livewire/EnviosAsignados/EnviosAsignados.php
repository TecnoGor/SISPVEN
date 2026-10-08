<?php

namespace App\Livewire\EnviosAsignados;
use Livewire\Attributes\Layout;

use Livewire\Component;

#[Layout('layouts.app')]
class EnviosAsignados extends Component
{
    public function render()
    {
        return view('livewire.envios-asignados.envios-asignados');
    }
}
