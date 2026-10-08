<?php

namespace App\Livewire\Oficinas;

use App\Models\User;
use App\Models\Estado;
use App\Models\Documento;
use App\Models\Empleado;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\UsuarioEstado;
use Livewire\WithFileUploads;
use GuzzleHttp\Promise\Create;
use App\Models\OficinaPersonal;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use App\Livewire\Forms\Usuarios\EditForm1;
use App\Livewire\Forms\OficinaDetalles\EditForm;
use App\Livewire\Forms\OficinaDetalles\CreateForm;
use App\Livewire\Forms\OficinaDetalles\CreateForm2;
use App\Models\TipoVehiculo;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class Integrantes extends Component
{

    use WithFileUploads;

    public $oficina_id;
    public $oficina;
    public $estadoNombre;
    public $municipioNombre;
    public $parroquiaNombre;
    public EditForm1 $editForm;
    public EditForm $EditForm2;
    public CreateForm $CreateForm;
    public CreateForm2 $CreateForm2;

    public $editMode = false;
    public $nombre;
    public $correo;
    public $telefono;
    private $originalValues = [];
    public $personalOficina;
    public $rolesDisponibles;
    public $editingId = null;
    public $mod = false;
    public $modificar_oficina_id;

    public function mount($oficina_id)
    {
        $this->oficina_id = $oficina_id;
        $this->oficina = Oficina::find($this->oficina_id);
        $oficina = Oficina::find($this->oficina_id);

        if ($this->oficina) {
            $estado = Estado::find($this->oficina->estado_id);
            $this->estadoNombre = $estado ? $estado->nombre : 'Desconocido';
            $municipio = Municipio::find($this->oficina->municipio_id);
            $this->municipioNombre = $municipio ? $municipio->nombre : 'Desconocido';
            $parroquia = Parroquia::find($this->oficina->parroquia_id);
            $this->parroquiaNombre = $parroquia ? $parroquia->nombre : 'Desconocido';
        }
    }

    public function modificar($id)
    {
        $this->modificar_oficina_id = $id;
        $this->mod = true;
    }

    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;

        // Si se cancela la edición, restaurar los valores originales
        if (!$this->editMode) {
            $this->resetFields();
        }
    }

    public function resetFields()
    {
        $this->nombre = $this->oficina['nombre'];
        $this->correo = $this->oficina['correo'];
        $this->telefono = $this->oficina['telefono'];
    }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email',
            'telefono' => 'required|string|max:20',
        ]);

        $this->oficina->update([
            'nombre' => $this->nombre,
            'correo' => $this->correo,
            'telefono' => $this->telefono,
        ]);

        $this->editMode = false;

        // Actualizar los valores originales
        $this->originalValues = [
            'nombre' => $this->oficina->nombre,
            'correo' => $this->oficina->correo,
            'telefono' => $this->oficina->telefono,
        ];

        session()->flash('message', 'Oficina actualizada correctamente.');
    }

    public function edit(User $usuario)
    {
        $this->resetValidation();
        $this->editForm->edit($usuario);
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Usuario editado exitosamente!');
    }

    public function edit2()
    {
        $this->resetValidation();
        $this->EditForm2->edit($this->oficina_id);
    }
    public function update2()
    {
        $this->EditForm2->update();
        $this->dispatch('alertSuccess', ['message' => 'Usuario editado exitosamente!']);
        $this->dispatch('tarifaUpdated');
    }

    public function create()
    {
        $this->resetValidation();
        $this->CreateForm->create($this->oficina_id);
    }

    public function validarRolSeleccionado()
    {
        if ($this->CreateForm->role_id) {
            $rolSeleccionado = Role::find($this->CreateForm->role_id);

            if ($rolSeleccionado) {
                $configuracionRol = OficinaPersonal::where('oficina_id', $this->oficina_id)
                    ->where('rol_id', $rolSeleccionado->id)
                    ->first();

                if ($configuracionRol) {
                    $usuariosConRol = User::where('oficina_id', $this->oficina_id)
                        ->whereHas('roles', function ($query) use ($rolSeleccionado) {
                            $query->where('id', $rolSeleccionado->id);
                        })
                        ->count();

                    if ($usuariosConRol >= $configuracionRol->cantidad_max) {
                        $this->addError('CreateForm.role_id', "El rol '{$rolSeleccionado->name}' ya ha alcanzado su límite máximo ({$configuracionRol->cantidad_max} usuarios).");
                        return;
                    }
                }
            }
        }

        $this->resetErrorBag('CreateForm.role_id');
    }

    public function store()
    {
        $result = $this->CreateForm->store();
        
        if ($result === 'ok') {
            $this->dispatch('alertSuccess', message: 'Integrante Creado Exitosamente!');
        }
    }
    public function create2()
    {
        $this->resetValidation();
        $this->CreateForm2->create($this->oficina_id);
    }

    public function store2()
    {
        $this->CreateForm2->store();
        $this->dispatch('alertSuccess', message: 'Vehiculo Registrado Exitosamente!');
    }

    public $rol_asignar = [];

    public function asignarRol($userId)
    {
        $rolId = $this->rol_asignar[$userId] ?? null;

        if (!$rolId) {
            $this->dispatch('alertError', message: 'Debe seleccionar un rol.');
            return;
        }

        $usuario = User::find($userId);
        $rol = Role::find($rolId);

        if (!$usuario || !$rol) {
            $this->dispatch('alertError', message: 'Usuario o rol no válido.');
            return;
        }

        // Validar cantidad_max
        $config = OficinaPersonal::where('oficina_id', $this->oficina_id)
            ->where('rol_id', $rol->id)
            ->first();

        if ($config) {
            $count = User::where('oficina_id', $this->oficina_id)
                ->whereHas('roles', fn($q) => $q->where('id', $rol->id))
                ->count();

            if ($count >= $config->cantidad_max) {
                $this->dispatch('alertError', message: "El rol '{$rol->name}' ya alcanzó su límite máximo ({$config->cantidad_max}).");
                return;
            }
        }

        $usuario->assignRole($rol);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se asignó el rol {$rol->name} al usuario ({$usuario->id}) en la oficina ({$this->oficina->nombre})",
        ]);

        $this->rol_asignar[$userId] = null;
        $this->dispatch('alertSuccess', message: "Rol '{$rol->name}' asignado correctamente a {$usuario->name}.");
    }

    public function desactivar(User $usuario)
    {
        $usuario->activo = false;
        $usuario->save();
        $this->dispatch('alertSuccess', message: 'Usuario Desactivado exitosamente!');
    }
    public function activar(User $usuario)
    {
        $usuario->activo = true;
        $usuario->save();
        $this->dispatch('alertSuccess', message: 'Usuario Activado exitosamente!');
    }


    public function render()
    {
        $usuarioIds = UsuarioEstado::where('id_estado', $this->oficina->estado_id)->pluck('id_user');

        $usuarios = User::with('roles')
            ->whereIn('id', $usuarioIds)
            ->whereHas('roles', function ($query) {
                $query->where('id', 3);
            })
            ->whereNull('oficina_id')
            ->get();

        $rolescantidad = OficinaPersonal::where('oficina_id', $this->oficina_id)
            ->get();
        $rolesIds = $rolescantidad->pluck('rol_id')->unique();
        $roles = Role::whereIn('id', $rolesIds)->get(['id', 'name']);
        $rolesNombres = $roles->pluck('name', 'id');

        $cantidad = $rolescantidad->pluck('cantidad_max');

        $oficinausuarios = User::where('oficina_id', $this->oficina_id)->get();

        $rolesCount = [];
        foreach ($roles as $rol) {
            $count = User::where('oficina_id', $this->oficina_id)
                ->whereHas('roles', function ($query) use ($rol) {
                    $query->where('id', $rol->id);
                })
                ->count();
            $rolesCount[$rol->id] = $count;
        }

        $rolChofer = Role::where('name', 'Chofer')->first();
        $tiposvehiculos = TipoVehiculo::all();

        $empleadosDisponibles = Empleado::where('oficina_id', $this->oficina_id)
            ->whereDoesntHave('user')
            ->orderBy('nombre')
            ->get();

        // Usuarios de esta oficina que no tienen ningún rol asignado
        $usuariosSinRol = User::where('oficina_id', $this->oficina_id)
            ->whereDoesntHave('roles')
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        return view('livewire.oficinas.integrantes', [
            'oficina' => $this->oficina,
            'usuarios' => $usuarios,
            'oficinausuarios' => $oficinausuarios,
            'rolescantidad' => $rolescantidad,
            'rolesNombres' => $rolesNombres,
            'cantidad' => $cantidad,
            'rolesCount' => $rolesCount,
            'rolChofer' => $rolChofer,
            'tipos' =>  $tiposvehiculos,
            'empleadosDisponibles' => $empleadosDisponibles,
            'usuariosSinRol' => $usuariosSinRol,
            'tiposDocumento' => Documento::whereIn('tipo', ['V', 'E'])->get(),
        ]);
    }
}
