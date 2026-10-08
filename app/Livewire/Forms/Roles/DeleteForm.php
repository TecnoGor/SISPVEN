<?php

namespace App\Livewire\Forms\Roles;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use Spatie\Permission\Models\Role;

class DeleteForm extends Form
{
    public function destroy(Role $role)
    {
        $role->delete();

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'delete',
            'descripcion' => "usuario $usuario->id hizo un delete del rol $role->id"
        ]);
    }
}
