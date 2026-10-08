<?php

namespace App\Livewire\OficinaOperativa;
use Livewire\Attributes\Layout;

use Livewire\Component;

#[Layout('layouts.app')]
class OficinaOperativa extends Component
{
    public function render()
    {
        return view('livewire.oficina-operativa.oficina-operativa');
    }
}
