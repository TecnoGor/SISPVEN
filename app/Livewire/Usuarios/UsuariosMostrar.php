<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use App\Models\Estado;
use App\Models\Oficina;
use App\Models\OficinaPersonal;
use App\Models\UsuarioEstado;
use App\Models\UsuarioSeguimiento;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class UsuariosMostrar extends Component
{
    use WithPagination;

    // Tabla y filtros
    public $search = '';
    public $filter = 'all';
    public $perPage = 10;
    public $filtroEstado = '';
    public $filtroOficina = '';

    // Modal crear usuario
    public $isOpen = false;
    public $name, $email, $password, $cedula, $tipoCedula;

    // Modal editar usuario (solo email + contraseña)
    public $modalEditar = false;
    public $editar_usuario_id;
    public $editar_nombre;
    public $editar_cedula;
    public $editar_email;
    public $editar_password;

    // Modal asignar rol
    public $modalRol = false;
    public $rol_usuario_id;
    public $rol_usuario_nombre;
    public $rol_usuario_oficina;
    public $rol_seleccionado;
    public $rolesDisponiblesOficina = [];

    // Modal transferir
    public $modalTransferir = false;
    public $transferir_usuario_id;
    public $transferir_usuario_nombre;
    public $transferir_estado_actual;
    public $transferir_oficina_actual;
    public $transferir_estado_destino;
    public $transferir_oficina_destino;
    public $oficinasDestino = [];

    // ==========================================
    // CREAR USUARIO
    // ==========================================

    public function open()
    {
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->reset(['name', 'email', 'password', 'cedula', 'tipoCedula']);
    }

    public function save()
    {
        $this->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'cedula'     => 'required|numeric',
            'tipoCedula' => 'required|in:V-,E-',
            'password'   => 'required|min:8',
        ], [
            'name.required'       => 'El nombre es obligatorio.',
            'email.required'      => 'El correo es obligatorio.',
            'email.unique'        => 'Este correo ya está registrado.',
            'cedula.required'     => 'La cédula es obligatoria.',
            'tipoCedula.required' => 'Seleccione el tipo de documento.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $cedulaCompleta = $this->tipoCedula . $this->cedula;

        if (User::where('cedula', $cedulaCompleta)->exists()) {
            $this->dispatch('alertError', message: 'Ya existe un usuario con esta cédula.');
            return;
        }

        User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'cedula'   => $cedulaCompleta,
            'password' => Hash::make($this->password),
        ]);

        $this->dispatch('alertSuccess', message: 'Usuario creado exitosamente.');
        $this->close();
    }

    // ==========================================
    // EDITAR USUARIO (solo email + contraseña)
    // ==========================================

    public function abrirEditar($userId)
    {
        $usuario = User::find($userId);
        $this->editar_usuario_id = $usuario->id;
        $this->editar_nombre = $usuario->name;
        $this->editar_cedula = $usuario->cedula;
        $this->editar_email = $usuario->email;
        $this->editar_password = '';
        $this->modalEditar = true;
    }

    public function cerrarEditar()
    {
        $this->modalEditar = false;
        $this->reset(['editar_usuario_id', 'editar_nombre', 'editar_cedula', 'editar_email', 'editar_password']);
    }

    public function guardarEdicion()
    {
        $this->validate([
            'editar_email'    => 'required|email|unique:users,email,' . $this->editar_usuario_id,
            'editar_password' => 'nullable|string|min:8',
        ], [
            'editar_email.required' => 'El correo es obligatorio.',
            'editar_email.email'    => 'El correo no es válido.',
            'editar_email.unique'   => 'Este correo ya está registrado por otro usuario.',
            'editar_password.min'   => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $usuario = User::find($this->editar_usuario_id);
        $usuario->email = $this->editar_email;

        if (!empty($this->editar_password)) {
            $usuario->password = Hash::make($this->editar_password);
        }

        $usuario->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se editó el correo/contraseña del usuario ({$usuario->id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Usuario actualizado correctamente.');
        $this->cerrarEditar();
    }

    // ==========================================
    // ASIGNAR ROL
    // ==========================================

    public function abrirAsignarRol($userId)
    {
        $usuario = User::find($userId);
        $this->rol_usuario_id = $usuario->id;
        $this->rol_usuario_nombre = $usuario->name;
        $this->rol_usuario_oficina = $usuario->oficina_id;
        $this->rol_seleccionado = $usuario->roles->first()?->id;

        // Cargar roles disponibles para la oficina del usuario
        $rolesOficina = OficinaPersonal::where('oficina_id', $usuario->oficina_id)->get();
        $rolesIds = $rolesOficina->pluck('rol_id')->toArray();
        $roles = Role::whereIn('id', $rolesIds)->get();

        $this->rolesDisponiblesOficina = $roles->map(function ($rol) use ($rolesOficina) {
            $config = $rolesOficina->where('rol_id', $rol->id)->first();
            $actual = User::where('oficina_id', $this->rol_usuario_oficina)
                ->whereHas('roles', fn($q) => $q->where('id', $rol->id))
                ->count();

            return [
                'id'        => $rol->id,
                'name'      => $rol->name,
                'actual'    => $actual,
                'max'       => $config->cantidad_max,
                'disponible' => $actual < $config->cantidad_max,
            ];
        })->toArray();

        $this->modalRol = true;
    }

    public function cerrarAsignarRol()
    {
        $this->modalRol = false;
        $this->reset(['rol_usuario_id', 'rol_usuario_nombre', 'rol_usuario_oficina', 'rol_seleccionado', 'rolesDisponiblesOficina']);
    }

    public function guardarRol()
    {
        $this->validate([
            'rol_seleccionado' => 'required',
        ], [
            'rol_seleccionado.required' => 'Debe seleccionar un rol.',
        ]);

        $usuario = User::find($this->rol_usuario_id);
        $rol = Role::find($this->rol_seleccionado);

        // Validar cantidad_max (excluyendo al propio usuario si ya tiene ese rol)
        $config = OficinaPersonal::where('oficina_id', $usuario->oficina_id)
            ->where('rol_id', $rol->id)
            ->first();

        if ($config) {
            $count = User::where('oficina_id', $usuario->oficina_id)
                ->where('id', '!=', $usuario->id)
                ->whereHas('roles', fn($q) => $q->where('id', $rol->id))
                ->count();

            if ($count >= $config->cantidad_max) {
                $this->dispatch('alertError', message: "El rol '{$rol->name}' ya alcanzó su límite ({$config->cantidad_max}) en esta oficina.");
                return;
            }
        }

        $usuario->syncRoles([$rol]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se asignó el rol {$rol->name} al usuario ({$usuario->id})",
        ]);

        $this->dispatch('alertSuccess', message: "Rol '{$rol->name}' asignado correctamente.");
        $this->cerrarAsignarRol();
    }

    // ==========================================
    // TRANSFERIR USUARIO
    // ==========================================

    public function abrirTransferir($userId)
    {
        $usuario = User::find($userId);
        $this->transferir_usuario_id = $usuario->id;
        $this->transferir_usuario_nombre = $usuario->name;
        $this->transferir_estado_actual = $usuario->oficina->estado_id ?? null;
        $this->transferir_oficina_actual = $usuario->oficina_id;
        $this->transferir_estado_destino = '';
        $this->transferir_oficina_destino = '';
        $this->oficinasDestino = [];
        $this->modalTransferir = true;
    }

    public function cerrarTransferir()
    {
        $this->modalTransferir = false;
        $this->reset(['transferir_usuario_id', 'transferir_usuario_nombre', 'transferir_estado_actual', 'transferir_oficina_actual', 'transferir_estado_destino', 'transferir_oficina_destino', 'oficinasDestino']);
    }

    public function updatedTransferirEstadoDestino($value)
    {
        $this->transferir_oficina_destino = '';
        $this->oficinasDestino = [];

        if ($value) {
            $this->oficinasDestino = Oficina::where('estado_id', $value)
                ->whereIn('tipo_oficina_id', [1, 2, 3, 4])
                ->where('externa', false)
                ->where('oficina_id', '!=', $this->transferir_oficina_actual)
                ->orderBy('nombre')
                ->get()
                ->toArray();
        }
    }

    public function confirmarTransferencia()
    {
        $this->validate([
            'transferir_estado_destino'  => 'required',
            'transferir_oficina_destino' => 'required',
        ], [
            'transferir_estado_destino.required'  => 'Debe seleccionar un estado destino.',
            'transferir_oficina_destino.required' => 'Debe seleccionar una oficina destino.',
        ]);

        if ($this->transferir_oficina_destino == $this->transferir_oficina_actual) {
            $this->dispatch('alertError', message: 'La oficina destino no puede ser la misma que la actual.');
            return;
        }

        $usuario = User::find($this->transferir_usuario_id);
        if (!$usuario) {
            $this->dispatch('alertError', message: 'Usuario no encontrado.');
            return;
        }

        $oficinaDestino = Oficina::find($this->transferir_oficina_destino);
        if (!$oficinaDestino) {
            $this->dispatch('alertError', message: 'Oficina destino no encontrada.');
            return;
        }

        $oficinaAnterior = $usuario->oficina?->nombre ?? '—';
        $rolAnterior = $usuario->roles->first()?->name ?? 'Sin rol';

        try {
            DB::transaction(function () use ($usuario, $oficinaDestino, $oficinaAnterior, $rolAnterior) {
                $usuario->syncRoles([]);
                $usuario->oficina_id = $oficinaDestino->oficina_id;
                $usuario->save();

                UsuarioEstado::updateOrCreate(
                    ['id_user' => $usuario->id],
                    ['id_estado' => $this->transferir_estado_destino]
                );

                UsuarioSeguimiento::create([
                    'usuario_id'  => auth()->user()->id,
                    'accion'      => 'update',
                    'descripcion' => "Se transfirió al usuario ({$usuario->id}) de {$oficinaAnterior} a {$oficinaDestino->nombre}. Rol anterior: {$rolAnterior}",
                ]);
            });

            $this->dispatch('alertSuccess', message: "Usuario transferido a {$oficinaDestino->nombre}. Queda sin rol hasta que la oficina destino le asigne uno.");
            $this->cerrarTransferir();
        } catch (\Throwable $e) {
            Log::error('Error al transferir usuario', [
                'usuario_id'         => $usuario->id,
                'oficina_destino_id' => $oficinaDestino->oficina_id,
                'error'              => $e->getMessage(),
            ]);
            $this->dispatch('alertError', message: 'Ocurrió un error al transferir el usuario. Intente de nuevo.');
        }
    }

    // ==========================================
    // ACTIVAR / DESACTIVAR
    // ==========================================

    public function desactivar(User $usuario)
    {
        $usuario->activo = false;
        $usuario->save();
        $this->dispatch('alertSuccess', message: 'Usuario desactivado exitosamente.');
    }

    public function activar(User $usuario)
    {
        $usuario->activo = true;
        $usuario->save();
        $this->dispatch('alertSuccess', message: 'Usuario activado exitosamente.');
    }

    // ==========================================
    // FILTROS
    // ==========================================

    public function updatedFiltroEstado()
    {
        $this->filtroOficina = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // ==========================================
    // RENDER
    // ==========================================

    public function render()
    {
        $authUser = auth()->user();
        $esSuperAdmin = $authUser->hasRole('SuperAdmin');
        $esGerenteEstado = $authUser->hasRole('Gerente de Estado');

        $estadosPermitidos = null;
        if ($esGerenteEstado) {
            $estadoGerenteId = $authUser->usuarioEstado?->id_estado;
            $estadoGerenteNombre = $estadoGerenteId
                ? Estado::where('estado_id', $estadoGerenteId)->value('nombre')
                : null;

            $paresEspeciales = [
                'Distrito Capital' => ['Distrito Capital', 'Miranda', 'La Guaira'],
                'Monagas'          => ['Monagas', 'Delta Amacuro'],
            ];

            $nombresPermitidos = $paresEspeciales[$estadoGerenteNombre] ?? ($estadoGerenteNombre ? [$estadoGerenteNombre] : []);
            $estadosPermitidos = Estado::whereIn('nombre', $nombresPermitidos)->pluck('estado_id')->toArray();
        }

        $estados = Estado::orderBy('nombre')
            ->when($esGerenteEstado, fn($q) => $q->whereIn('estado_id', $estadosPermitidos ?: [0]))
            ->get();

        $oficinas = Oficina::when($this->filtroEstado, function ($query) {
            $query->where('estado_id', $this->filtroEstado);
        })
            ->when($esGerenteEstado, fn($q) => $q->whereIn('estado_id', $estadosPermitidos ?: [0]))
            ->when(!$esSuperAdmin && !$esGerenteEstado, fn($q) => $q->where('oficina_id', $authUser->oficina_id))
            ->orderBy('nombre')->get();

        $roles = Role::whereNotIn('id', [38, 39, 40, 41, 42])->get();

        $usuarios = User::with(['oficina', 'roles'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('email', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('cedula', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->when($this->filter && $this->filter !== 'all', function ($query) {
                if ($this->filter === 'sin_rol') {
                    $query->whereDoesntHave('roles');
                } else {
                    $query->whereHas('roles', function ($q) {
                        $q->where('name', $this->filter);
                    });
                }
            })
            ->when($this->filtroEstado, function ($query) {
                $query->whereHas('oficina', function ($q) {
                    $q->where('estado_id', $this->filtroEstado);
                });
            })
            ->when($this->filtroOficina, function ($query) {
                $query->where('oficina_id', $this->filtroOficina);
            })
            ->when($esGerenteEstado, function ($query) use ($estadosPermitidos) {
                $query->whereHas('oficina', function ($q) use ($estadosPermitidos) {
                    $q->whereIn('estado_id', $estadosPermitidos ?: [0]);
                });
            })
            ->when(!$esSuperAdmin && !$esGerenteEstado, function ($query) use ($authUser) {
                $query->where('oficina_id', $authUser->oficina_id);
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.usuarios.usuarios-mostrar', compact('usuarios', 'roles', 'oficinas', 'estados'));
    }
}
