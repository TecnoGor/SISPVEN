<?php

namespace App\Livewire\Forms\Roles;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use Spatie\Permission\Models\Role;

class EditForm extends Form
{
    public $role_id = '';
    public $open = false;

    #[Validate('required|string|min:3|max:255')]
    public $name;

    

    public function edit(Role $role)
    {
        $this->open = true;
        $this->role_id = $role->id;

        $this->name = $role->name;
    }

    public function update()
    {
        $this->validate();

        $role = Role::find($this->role_id);

        $role->update(
            $this->all()
        );

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'update',
            'descripcion' => "Usuario {$usuario->id} hizo un update del rol {$role->id}"
        ]);
        
        
        // Cambiar el estado de la propiedad open
        $this->open = false;
    }
    
}
