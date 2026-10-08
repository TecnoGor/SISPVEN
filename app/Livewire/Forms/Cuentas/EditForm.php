<?php

namespace App\Livewire\Forms\Cuentas;

use Livewire\Form;
use App\Models\User;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm extends Form
{
    public $open = false;
    public $usuario_id = '';
    public $oficina;
    public $role; // Variable para almacenar el ID del rol seleccionado

    public function edit(User $usuario)
    {
        $this->open = true;
        $this->usuario_id = $usuario->id;
        $this->oficina = $usuario->oficina_id;
        $this->role = $usuario->roles()->first()?->id;
    } 

    public function update()
    {
        $this->validate([
            'oficina' => 'required',
            'role' => 'required|exists:roles,id',
        ]);

        // Buscar el usuario a actualizar
    $usuario = User::find($this->usuario_id);

    // Actualizar la oficina
    $usuario->oficina_id = $this->oficina;

    // Verifica si el rol existe para el guard `web`
    $role = \Spatie\Permission\Models\Role::findById($this->role, 'web'); // Aquí especificamos el guard

    // Si el rol es encontrado, asignarlo al usuario
    if ($role) {
        $usuario->syncRoles([$role]);
    } else {
        // Si no se encuentra el rol, lanzar un error o manejar el caso
        session()->flash('error', 'El rol no existe para el guard web.');
        return;
    }

    // Guardar los cambios
    $usuario->save();
        // Registrar seguimiento
        $user = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'update',
            'descripcion' => "Usuario $user->id hizo un update del usuario $usuario->id",
        ]);

        $this->open = false;
    }
}
