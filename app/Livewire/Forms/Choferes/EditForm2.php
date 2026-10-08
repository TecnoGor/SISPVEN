<?php

namespace App\Livewire\Forms\Choferes;

use Livewire\Form;
use App\Models\Chofer;
use App\Models\Vehiculo;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm2 extends Form
{
    public $open = false;
    public $chofer_id = '';
    public $selectedVehiculoId;

    public function edit(Chofer $chofer)
    {
        $this->open = true;
        $this->chofer_id = $chofer->chofer_id;
    }

    public function update()
    {
        $vehiculo = Vehiculo::find($this->selectedVehiculoId);
        
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