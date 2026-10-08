<?php

namespace App\Livewire\Forms\Vehiculos;

use Livewire\Form;
use App\Models\Vehiculo;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm2 extends Form
{
    public $open = false;
    public $vehiculo_id = '';
    public $chofer_id;

    public function edit(Vehiculo $vehiculo)
    {
        $this->open = true;
        $this->vehiculo_id = $vehiculo->vehiculo_id;
        $this->chofer_id = $vehiculo->chofer_id;
    }

    public function update()
    {
        $vehiculo = Vehiculo::find($this->vehiculo_id );
        
        $vehiculo->chofer_id = $this->chofer_id;

        $vehiculo->save();

        $user = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $user->id,
            'accion' => 'update',
            'descripcion' => "Usuario $user->id asigno un vehiculo a un chofer"
        ]);

        $this->open = false;
    }
}