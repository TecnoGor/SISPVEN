<?php

namespace App\Livewire\Forms\Oficinas;

use Livewire\Form;

class Createform2 extends Form
{
    public $open = false;
    
    public function create()
    {
        $this->open = true;
    }
}
