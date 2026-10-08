<?php

namespace App\Livewire\Forms\ControlFlota;

use App\Models\Vehiculo;
use Livewire\Attributes\Validate;
use Livewire\Form;

class editForm extends Form
{

    public $vehiculo_id = '';
    public $open = false;

    public function edit(Vehiculo $vehiculo)
    {
        $this->open = true;
    }


    public function update()
    {
        $this->validate();
        $this->open = false;
    }
}
