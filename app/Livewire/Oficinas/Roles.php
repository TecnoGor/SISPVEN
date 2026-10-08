<?php

namespace App\Livewire\Oficinas;

use DB;
use App\Models\User;
use App\Models\Estado;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Municipio;
use App\Models\Parroquia;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\UsuarioEstado;
use Livewire\WithFileUploads;
use App\Models\OficinaPersonal;
use App\Models\RoleTipoOficina;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Role;
use App\Livewire\Forms\OficinaDetalles\EditForm;
use App\Livewire\Forms\OficinaDetalles\CreateForm;
use App\Livewire\Forms\OficinaDetalles\DeleteForm;
use App\Livewire\Forms\OficinaDetalles\CreateForm2;
use App\Livewire\Forms\OficinaDetalles\CreateForm3;
use App\Models\TipoVehiculo;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]

class Roles extends Component
{


    use WithPagination;

    public $oficina_id;
    public $oficina;
    public $estadoNombre;
    public $municipioNombre;
    public $parroquiaNombre;
    public EditForm $EditForm;
    public CreateForm $CreateForm;
    public CreateForm2 $CreateForm2;
    public CreateForm3 $CreateForm3;
    public DeleteForm $deleteForm;
    public $editMode = false;
    public $nombre;
    public $rolesOficina;
    public $correo;
    public $telefono;
    #[Validate('image|max:1024')] // 1MB Max
    public $photo;
    private $originalValues = [];
    public $personalOficina;
    public $rolesDisponibles;

    public $editingId = null;
    public $cantidadMaxEdit;

    public function edit2($id, $currentCantidadMax)
    {
        $this->editingId = $id;
        $this->cantidadMaxEdit = $currentCantidadMax;
    }

    public function updateCantidadMax($id)
    {
        $this->validate([
            'cantidadMaxEdit' => 'required|numeric|min:0',
        ], [
            'cantidadMaxEdit.min' => 'El valor debe ser mayor o igual a cero.',
        ]);

        $rol = OficinaPersonal::findOrFail($id);
        $rol->update(['cantidad_max' => $this->cantidadMaxEdit]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se modificó la cantidad máxima del rol ({$id}) a {$this->cantidadMaxEdit} en la oficina ({$this->oficina_id})",
        ]);

        $this->editingId = null; // Salir del modo edición
        $this->cantidadMaxEdit = null; // Limpiar el valor editado
        $this->dispatch('alertSuccess', message: 'Cantidad máxima actualizada exitosamente.');
    }


    public function cancelEdit()
    {
        $this->editingId = null; // Salir del modo edición
        $this->cantidadMaxEdit = null; // Limpiar el valor editado
    }


    public function mount($oficina_id)
    {
        $usuario = auth()->user();

        // Obtener los roles ya asignados a la oficina
        $assignedRoles = OficinaPersonal::where('oficina_id', $oficina_id)
            ->pluck('rol_id') // Obtener solo los IDs de los roles asignados
            ->toArray(); // Convertir la colección en un array

        // Consultar todos los roles del sistema y descartar los que ya están asignados, y también 1, 2 y 3
        $rolesDisponibles = Role::whereNotIn('id', array_merge($assignedRoles, [1, 2, 3, 17, 38, 39, 40, 41, 42]))->get();

        $this->rolesOficina = $rolesDisponibles;
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

        $this->personalOficina = OficinaPersonal::where('oficina_id', $this->oficina_id)->get();
    }

    public function mount2($oficina)
    {
        $this->oficina = $oficina;
        $this->nombre = $oficina->nombre;
        $this->correo = $oficina->correo;
        $this->telefono = $oficina->telefono;

        // Almacenar los valores originales
        $this->originalValues = [
            'nombre' => $oficina->nombre,
            'correo' => $oficina->correo,
            'telefono' => $oficina->telefono,
        ];
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



    public function edit()
    {
        $this->resetValidation();
        $this->EditForm->edit($this->oficina_id);
    }
    public function update()
    {
        $this->EditForm->update();
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
                        session()->flash('error', 'Este rol ya ha alcanzado su límite máximo de usuarios (' . $configuracionRol->cantidad_max . ').');
                    } else {
                        session()->forget('error');
                    }
                }
            }
        }
    }

    public function store()
    {
        // El formulario CreateForm ya maneja todas las validaciones
        // incluyendo cédula duplicada y límite de roles
        $this->CreateForm->store();

        // Solo mostrar mensaje de éxito si no hay errores
        if (!session()->has('error') && !session()->has('alert')) {
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

    public function create3()
    {
        $this->resetValidation();
        $this->CreateForm3->create($this->oficina_id);
    }

    public function store3()
    {
        $this->CreateForm3->store();
        $this->dispatch('alertSuccess', message: 'Rol Agregado Existosamente!');
        $this->dispatch('tarifaUpdated');
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


    #[On('delete')]
    public function delete(OficinaPersonal $rol)
    {
        $rol->delete(); // Elimina el registro
        $this->dispatch('alertSuccess', message: 'Rol eliminado exitosamente!');
        $this->dispatch('tarifaUpdated');
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

        return view('livewire.oficinas.roles', [
            'roleOficina' =>  $this->rolesOficina,
            'roles' =>  $this->personalOficina,
            'oficina' => $this->oficina,
            'usuarios' => $usuarios,
            'oficinausuarios' => $oficinausuarios,
            'rolescantidad' => $rolescantidad,
            'rolesNombres' => $rolesNombres,
            'cantidad' => $cantidad,
            'rolesCount' => $rolesCount,
            'tipos' =>  $tiposvehiculos,
            'rolChofer' => $rolChofer,

        ]);
    }
}
