<?php

namespace App\Livewire\Forms\Vehiculos;

use Livewire\Form;
use App\Models\Vehiculo;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm3 extends Form
{
    public $open = false;
    public $vehiculo_id = '';
    public $user_id;

    public function edit(Vehiculo $vehiculo)
    {
        $this->open = true;
        $this->vehiculo_id = $vehiculo->vehiculo_id;
        $this->user_id = $vehiculo->user_id;
    }

    public function update()
    {
        $vehiculo = Vehiculo::find($this->vehiculo_id );
        
        $vehiculo->user_id = $this->user_id;

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
