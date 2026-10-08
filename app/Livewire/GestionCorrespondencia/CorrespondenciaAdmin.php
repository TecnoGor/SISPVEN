<?php

namespace App\Livewire\GestionCorrespondencia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CorrespondenciaAdmin extends Component
{
    use WithPagination;

    public $current_view = 'dashboard';
    public $current_role = 'Administrador';

    // --- User Management ---
    public $search_query = '';
    public $filter_rol = '';
    public $perPage = 10;

    public $editing_user_id = null;
    public $show_delete_confirm = false;
    public $delete_target_id = null;

    public $user_id = '';
    public $user_search = ''; // Para vincular con datalist
    public $rol_correspondencia = '';

    public $correspondencia_roles = [
        'Presidente Correspondencia',
        'Director Correspondencia',
        'Gerente Correspondencia',
        'Analista Correspondencia',
        'Usuario Correspondencia'
    ];

    // Roles jerárquicos que el Admin puede asignar manualmente
    public $roles_asignables = [
        'Presidente Correspondencia',
        'Director Correspondencia',
        'Gerente Correspondencia',
        'Analista Correspondencia',
        'Usuario Correspondencia'
    ];

    // --- Dashboard Stats ---
    public $dashboard_stats = [];

    public function mount()
    {
        $this->refresh_stats();
    }

    public function open_dashboard()
    {
        $this->current_view = 'dashboard';
        $this->refresh_stats();
    }

    public function open_users()
    {
        $this->current_view = 'users';
        $this->search_query = '';
        $this->filter_rol = '';
        $this->resetPage();
    }

    public function open_create()
    {
        $this->current_view = 'create';
        $this->editing_user_id = null;
        $this->user_id = '';
        $this->user_search = '';
        $this->rol_correspondencia = '';
    }

    public function edit_user($id)
    {
        $user = User::find($id);
        if (!$user) return;

        $this->editing_user_id = $id;
        $this->user_id = $user->id;
        $this->user_search = "{$user->name} ({$user->email})";
        
        foreach ($this->correspondencia_roles as $roleName) {
            if ($user->hasRole($roleName)) {
                $this->rol_correspondencia = $roleName;
                break;
            }
        }
        
        $this->current_view = 'edit';
    }

    public function updatedUserSearch($value)
    {
        // Intentar emparejar el formato "Nombre (email)"
        if (preg_match('/^(.*) \((.*)\)$/', $value, $matches)) {
            $email = $matches[2];
            $user = User::where('email', $email)->first();
            if ($user) {
                $this->user_id = $user->id;
                return;
            }
        }
        
        // Si no hay paréntesis, intentar buscar por nombre exacto o email exacto
        $user = User::where('email', $value)->orWhere('name', $value)->first();
        if ($user) {
            $this->user_id = $user->id;
        } else {
            $this->user_id = null;
        }
    }

    public function select_suggested_user($id, $name, $email)
    {
        $this->user_id = $id;
        $this->user_search = "$name ($email)";
    }

    public function save_user()
    {
        if (empty($this->user_search)) {
            $this->user_id = null;
        }

        $this->validate([
            'user_id' => 'required|exists:users,id',
            'rol_correspondencia' => 'required|string'
        ], [
            'user_id.required' => 'Debe seleccionar un colaborador válido de la lista.'
        ]);

        $user = User::find($this->user_id);
        if (!$user) return;

        // Remover roles previos de correspondencia sin tocar los operativos
        foreach ($this->correspondencia_roles as $roleName) {
            if ($user->hasRole($roleName)) {
                $user->removeRole($roleName);
            }
        }

        // Asignar el nuevo rol jerárquico
        $user->assignRole($this->rol_correspondencia);

        // Limpieza de Caché de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->reset(['user_id', 'rol_correspondencia', 'editing_user_id']);
        $this->current_view = 'users';
        $this->refresh_stats();

        $this->dispatch('toastr', ['type' => 'success', 'message' => 'Rol de correspondencia asignado exitosamente.']);
    }

    public function confirm_delete($id)
    {
        $this->show_delete_confirm = true;
        $this->delete_target_id = $id;
    }

    public function cancel_delete()
    {
        $this->show_delete_confirm = false;
        $this->delete_target_id = null;
    }

    public function delete_user()
    {
        $user = User::find($this->delete_target_id);
        if ($user) {
            // Remover todos los roles de correspondencia
            foreach ($this->roles_asignables as $roleName) {
                if ($user->hasRole($roleName)) {
                    $user->removeRole($roleName);
                }
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }

        $this->show_delete_confirm = false;
        $this->delete_target_id = null;
        $this->refresh_stats();

        $this->dispatch('toastr', ['type' => 'success', 'message' => 'Acceso al módulo de correspondencia revocado completamente.']);
    }

    private function refresh_stats()
    {
        $pres = User::role('Presidente Correspondencia')->count();
        $dir = User::role('Director Correspondencia')->count();
        $ger = User::role('Gerente Correspondencia')->count();

        $this->dashboard_stats = [
            'total_habilitados' => User::role($this->correspondencia_roles)->count(),
            'candidatos' => User::whereDoesntHave('roles', function($q) {
                $q->whereIn('name', $this->correspondencia_roles);
            })->count(),
            'staff' => $pres + $dir + $ger,
            'presidentes' => $pres,
            'directores' => $dir,
            'gerentes' => $ger,
            'analistas' => User::role('Analista Correspondencia')->count(),
            'usuarios_reg' => User::role('Usuario Correspondencia')->count(),
            'ultimas_altas' => User::role($this->correspondencia_roles)->latest()->take(5)->get(),
        ];
    }

    public function updatedSearchQuery()
    {
        $this->resetPage();
    }

    public function updatedFilterRol()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $usuarios_query = User::role($this->correspondencia_roles);

        if ($this->search_query) {
            $usuarios_query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search_query . '%')
                  ->orWhere('email', 'like', '%' . $this->search_query . '%');
            });
        }

        if ($this->filter_rol) {
            $usuarios_query->role($this->filter_rol);
        }

        // Solo excluir usuarios que ya tienen un rol jerárquico asignado
        // Los que solo tienen "Usuario Correspondencia" sí deben aparecer (son candidatos a ascender)
        $available_users_query = User::orderBy('name')
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', $this->roles_asignables);
            });

        // Filtrar por texto de búsqueda si el usuario está escribiendo
        if ($this->user_search && !$this->user_id) {
            $available_users_query->where(function($q) {
                $q->where('name', 'like', '%' . $this->user_search . '%')
                  ->orWhere('email', 'like', '%' . $this->user_search . '%');
            });
        }

        return view('livewire.gestion-correspondencia.correspondencia-admin', [
            'usuarios_filtrados' => $usuarios_query->paginate($this->perPage),
            'available_users'    => $available_users_query->take(8)->get()
        ]);
    }
}

