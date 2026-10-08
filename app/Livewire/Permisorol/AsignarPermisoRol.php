<?php

namespace App\Livewire\PermisoRol;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\UsuarioSeguimiento;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AsignarPermisoRol extends Component
{
    public $role, $rol_permisos, $permisos;
    public $search = '';

    // protected $listeners = ['asignarPermisoRol'];

    #[On('asignarPermisoRol')]
    public function asignarPermisoRol($role_id, $permiso_id)
    {
        $role = Role::findOrFail($role_id);
        $permiso = Permission::findOrFail($permiso_id);

        $role->givePermissionTo($permiso);

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'update',
            'descripcion' => "usuario $usuario->usuario_id hizo un update y asigno el permiso $permiso->id al rol $role->id"
        ]);
       
        $this->rol_permisos = $this->role->permissions->pluck('name');
        $this->permisos = Permission::whereNotIn('name', $this->rol_permisos)
            ->where('name', 'NOT LIKE', 'Ver Correspondencia-%')
            ->where('name', '!=', 'Acceso Modulo Correspondencia')
            ->where('name', '!=', 'Seguimiento de Instrucciones Asignadas')
            ->where('name', 'LIKE', '%'.$this->search.'%')
            ->get()
            ->sortBy('name');
        $this->dispatch('refreshPermisos');
        $this->dispatch('alertSuccess', message: 'Permiso asignado exitosamente!');
    }

    #[On('refreshPermisosRol')]
    public function  refreshPermisosRol()
    {
        $this->rol_permisos = $this->role->permissions->pluck('name');
        $this->permisos = Permission::whereNotIn('name', $this->rol_permisos)
            ->where('name', 'NOT LIKE', 'Ver Correspondencia-%')
            ->where('name', '!=', 'Acceso Modulo Correspondencia')
            ->where('name', '!=', 'Seguimiento de Instrucciones Asignadas')
            ->where('name', 'LIKE', '%'.$this->search.'%')
            ->get()
            ->sortBy('name');
        $this->dispatch('refreshPermisos');
        
    }

    public function render()
    {
        $this->rol_permisos = $this->role->permissions->pluck('name');
        $this->permisos = Permission::whereNotIn('name', $this->rol_permisos)
        ->where('name', 'NOT LIKE', 'Ver Correspondencia-%')
        ->where('name', '!=', 'Acceso Modulo Correspondencia')
        ->where('name', '!=', 'Seguimiento de Instrucciones Asignadas')
        ->where('name', 'LIKE', '%'.$this->search.'%')
        ->get()
        ->sortBy('name');
        
        return view('livewire.permisorol.asignar-permiso-rol');
    }
}
