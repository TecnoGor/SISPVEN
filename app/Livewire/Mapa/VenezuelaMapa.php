<?php

namespace App\Livewire\Mapa;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class VenezuelaMapa extends Component
{
    public function render()
    {
        return view('livewire.mapa.venezuela-mapa');
    }
}
