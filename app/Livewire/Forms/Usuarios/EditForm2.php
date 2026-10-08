<?php
namespace App\Livewire\Forms\Usuarios;

use Livewire\Form;
use App\Models\User;
use App\Models\Estado;
use App\Models\Oficina;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use Spatie\Permission\Models\Role;
use App\Models\UsuarioEstado; // Asegúrate de importar el modelo correcto

class EditForm2 extends Form
{
    public $servicio;
    public $usuario_id = '';
    public $role_id = '';
    public $estado_id = '';
    public $open = false;
    public $roles = [];

    public $rol;

    public $estado;

    public function edit2(User $usuario)
    {
        $this->open = true;
        $this->usuario_id = $usuario->id;
    }

    public function update()
    {
        $role = Role::findOrFail($this->only('rol'))[0];
        $estado = Estado::findOrFail($this->only('estado'))[0];

        $usuario = User::find($this->usuario_id);
        if ($role->name != $usuario->getRoleNames()->first()) {
            if ($usuario->getRoleNames()->first()) {
                $usuario->removeRole($usuario->getRoleNames()->first());
            }
            $usuario->assignRole($role);
        }

        $usuario->update(
            $this->all()
        );

        // Registrar el cambio de estado en la tabla usuario_estados
        UsuarioEstado::updateOrCreate(
            ['id_user' => $usuario->id], // Condición para buscar el registro
            ['id_estado' => $estado->estado_id] // Datos a actualizar o crear
        );
        
        $user = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'update',
            'descripcion' => "usuario $user->id hizo un update del usuario $usuario->id"
        ]);
        
        $this->open = false;
    }

    public function render()
{
    return view('livewire.usuarios.usuarios-mostrar', [
        'roles' => $this->roles,
        'oficinas' => Oficina::all(),
    ]);
}
}
