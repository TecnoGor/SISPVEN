<?php

namespace App\Livewire\Forms\OficinaDetalles;

use App\Models\Oficina;
use App\Models\User;
use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm extends Form
{
    public $usuario_id = '';
    public $role_id = '';
    public $estado_id = '';
    public $open = false;
    public $id_oficina;  
    public $jefe;

public function edit($oficina_id)
{
    $this->open = true;
    $this->id_oficina = $oficina_id; // Asignar el id_oficina a una propiedad
}

public function update()
{
    $jefe = $this->jefe;
    $usuario = User::findOrFail($jefe);

    // Actualiza el campo oficina_id junto con otros campos del usuario
    $usuario->update(array_merge(
        $this->all(),
        ['oficina_id' => $this->id_oficina] // Añade el campo oficina_id
    ));

    // Obtener la oficina correspondiente al id_oficina
    $oficina = Oficina::findOrFail($this->id_oficina);

    // Actualiza el campo jefe_oficina de la oficina
    $oficina->update([
        'jefe_oficina' => $jefe // Asigna el valor de la variable jefe
    ]);

        $user = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'update',
            'descripcion' => "usuario $user->id hizo un update del usuario $usuario->id"
        ]);
        
        $this->open = false;
    }
}
