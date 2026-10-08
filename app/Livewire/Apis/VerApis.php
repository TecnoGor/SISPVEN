<?php

namespace App\Livewire\Apis;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class VerApis extends Component
{
    public function render()
    {
        return view('livewire.ver-apis.ver-apis');
    }
}
