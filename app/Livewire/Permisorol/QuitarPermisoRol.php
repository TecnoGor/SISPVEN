<?php

namespace App\Livewire\PermisoRol;

use Livewire\Component;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class QuitarPermisoRol extends Component
{
    public $role, $rol_permisos, $permisos;
    public $search = '';

    // protected $listeners = ['quitarPermisoRol'];

    #[On('quitarPermisoRol')]
    public function quitarPermisoRol($role_id, $permiso_id)
    {
        $role = Role::findOrFail($role_id);
        $permiso = Permission::findOrFail($permiso_id);

        $role->revokePermissionTo($permiso);

        $this->rol_permisos = $this->role->permissions->pluck('name');
        $this->permisos = Permission::whereIn('name', $this->rol_permisos)
            ->where('name', 'NOT LIKE', 'Ver Correspondencia-%')
            ->where('name', '!=', 'Acceso Modulo Correspondencia')
            ->where('name', '!=', 'Seguimiento de Instrucciones Asignadas')
            ->where('name', 'LIKE', '%'.$this->search.'%')
            ->get()
            ->sortBy('name');
        $this->dispatch('refreshPermisosRol');
        $this->dispatch('alertSuccess', message: 'Permiso revocado exitosamente!');
    }

    #[On('refreshPermisos')]
    public function refreshPermisos()
    {
        $this->rol_permisos = $this->role->permissions->pluck('name');

        $this->permisos = Permission::whereIn('name', $this->rol_permisos)
            ->where('name', 'NOT LIKE', 'Ver Correspondencia-%')
            ->where('name', '!=', 'Acceso Modulo Correspondencia')
            ->where('name', '!=', 'Seguimiento de Instrucciones Asignadas')
            ->where('name', 'LIKE', '%'.$this->search.'%')
            ->get()
            ->sortBy('name');
    }

    public function render()
    {
        $this->rol_permisos = $this->role->permissions->pluck('name');

        $this->permisos = Permission::whereIn('name', $this->rol_permisos)
        ->where('name', 'NOT LIKE', 'Ver Correspondencia-%')
        ->where('name', '!=', 'Acceso Modulo Correspondencia')
        ->where('name', '!=', 'Seguimiento de Instrucciones Asignadas')
        ->where('name', 'LIKE', '%'.$this->search.'%')
        ->get()
        ->sortBy('name');

        return view('livewire.permisorol.quitar-permiso-rol');
    }
}
