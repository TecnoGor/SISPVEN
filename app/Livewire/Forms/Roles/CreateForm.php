<?php

namespace App\Livewire\Forms\Roles;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use App\Models\RoleTipoOficina;
use Spatie\Permission\Models\Role;

class CreateForm extends Form
{
    public $open = false;
    
    #[Validate('required|array|min:1')]
    public $tipo_oficina = [];

    #[Validate('required|string|min:3|max:255|unique:'.Role::class)]
    public $name;

    public function create()
    {
        $this->open = true;
    }

    public function store()
    {
        $this->validate();

        $nombre = ucwords(strtolower($this->name));

        $role = Role::create(['name' => $nombre, 'guard_name' => 'web']);

        foreach($this->tipo_oficina as $oficina_id){
             RoleTipoOficina::create(['rol_id' => $role->id, 'tipo_oficina_id' => $oficina_id]);
        }
       

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'create',
            'descripcion' => "usuario $usuario->id hizo un create del rol $role->id"
        ]);
        $this->reset();
        $this->open = false;
    }
}  