<?php

namespace App\Livewire\Oficinas;

use App\Models\User;
use App\Models\Estado;
use App\Models\Documento;
use App\Models\Empleado;
use App\Models\Oficina;
use App\Models\Vehiculo;
use App\Models\Chofer;
use Livewire\Component;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\UsuarioEstado;
use App\Models\OficinaPersonal;
use App\Models\RoleTipoOficina;
use App\Models\ServicioFlota;
use App\Models\TipoVehiculo;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use App\Livewire\Forms\OficinaDetalles\EditForm;
use App\Livewire\Forms\OficinaDetalles\CreateForm;
use App\Livewire\Forms\OficinaDetalles\CreateForm2;
use App\Livewire\Forms\OficinaDetalles\CreateForm3;
use App\Livewire\Forms\OficinaDetalles\DeleteForm;
use App\Livewire\Forms\Usuarios\EditForm1;
use App\Livewire\Forms\Vehiculos\EditForm as VehiculoEditForm;
use App\Livewire\Forms\Vehiculos\EditForm3 as VehiculoEditForm3;
use App\Livewire\Forms\Vehiculos\CreateForm3 as VehiculoCreateForm3;

#[Layout('layouts.app')]
class MiOficina extends Component
{
    use WithFileUploads;
    use WithPagination;

    // ==========================================
    // TAB CONTROL
    // ==========================================
    public $tab = 'integrantes';

    // ==========================================
    // OFICINA BASE
    // ==========================================
    public $oficina_id;
    public $oficina;
    public $estadoNombre;
    public $municipioNombre;
    public $parroquiaNombre;
    public $mod = false;
    public $modificar_oficina_id;
    public $oficina_vehiculo;
    public $modal_vehiculo = false;

    // ==========================================
    // FORMS — OFICINA DETALLES
    // ==========================================
    public EditForm $EditForm;
    public CreateForm $CreateForm;
    public CreateForm2 $CreateForm2;

    // ==========================================
    // MODAL VINCULAR EMPLEADO
    // ==========================================
    public $modalVincular = false;
    public $vincular_usuario_id;
    public $vincular_cedula_buscar = '';
    public $vincular_empleado_encontrado = null;

    // ==========================================
    // TAB INTEGRANTES
    // ==========================================
    public EditForm1 $editForm;
    public $rol_asignar = [];

    // ==========================================
    // TAB VEHÍCULOS
    // ==========================================
    public VehiculoEditForm $editFormVehiculo;
    public VehiculoEditForm3 $editFormChofer;
    public VehiculoCreateForm3 $CreateForm3Vehiculo;
    public $search = '';
    public $perPage = 5;
    public $sortBy = 'vehiculo_id';
    public $sortDir = 'ASC';
    public $serviciosActivos;
    public $servicios = [
        ['servicio_id' => '', 'fecha' => '']
    ];
    public $combustibleModal = ['open' => false];
    public $combustibleForm = [
        'vehiculo_id' => null,
        'fecha' => '',
        'litros' => '',
        'costo_total' => '',
        'kilometraje' => '',
    ];

    // ==========================================
    // TAB ROLES
    // ==========================================
    public CreateForm3 $CreateForm3Rol;
    public $rolesOficina;
    public $personalOficina;
    public $editingId = null;
    public $cantidadMaxEdit;

    // ==========================================
    // MOUNT
    // ==========================================
    public function mount()
    {
        $usuario = auth()->user();
        $this->oficina_id = $usuario->oficina_id;
        $this->oficina = Oficina::find($this->oficina_id);

        if ($this->oficina) {
            $this->estadoNombre = optional(Estado::find($this->oficina->estado_id))->nombre ?? 'Desconocido';
            $this->municipioNombre = optional(Municipio::find($this->oficina->municipio_id))->nombre ?? 'Desconocido';
            $this->parroquiaNombre = optional(Parroquia::find($this->oficina->parroquia_id))->nombre ?? 'Desconocido';
        } else {
            $this->estadoNombre = 'Desconocido';
            $this->municipioNombre = 'Desconocido';
            $this->parroquiaNombre = 'Desconocido';
            session()->flash('error', 'No se encontró la oficina asociada al usuario.');
        }

        // Datos para tab Roles
        $assignedRoles = OficinaPersonal::where('oficina_id', $this->oficina_id)
            ->pluck('rol_id')
            ->toArray();

        $this->rolesOficina = Role::whereNotIn('id', array_merge($assignedRoles, [1, 2, 3, 17, 38, 39, 40, 41, 42]))->get();
        $this->personalOficina = OficinaPersonal::where('oficina_id', $this->oficina_id)->get();
    }

    // ==========================================
    // TAB NAVIGATION
    // ==========================================
    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    // ==========================================
    // OPERACIONES
    // ==========================================
    public function cambiar_operacion()
    {
        $this->oficina->operaciones = !$this->oficina->operaciones;
        $this->oficina->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se " . ($this->oficina->operaciones ? 'abrieron' : 'cerraron') . " las operaciones de la oficina ({$this->oficina->oficina_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Actividades Iniciadas/Finalizadas en la oficina!');
    }

    public function modificar($id)
    {
        $this->modificar_oficina_id = $id;
        $this->mod = true;
    }

    public function vehiculo_externo($id)
    {
        $this->oficina_vehiculo = $id;
        $this->modal_vehiculo = true;
    }

    // ==========================================
    // ASIGNAR JEFE DE OFICINA
    // ==========================================
    public function edit()
    {
        $this->resetValidation();
        $this->EditForm->edit($this->oficina_id);
    }

    public function update()
    {
        $this->EditForm->update();
        $this->dispatch('alertSuccess', ['message' => 'Jefe de oficina asignado exitosamente!']);
        $this->dispatch('tarifaUpdated');
    }

    // ==========================================
    // CREAR INTEGRANTE
    // ==========================================
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

    // ==========================================
    // REGISTRAR VEHÍCULO
    // ==========================================
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

    // ==========================================
    // VINCULAR EMPLEADO
    // ==========================================
    public function abrirVincular($userId)
    {
        $this->vincular_usuario_id = $userId;
        $this->vincular_cedula_buscar = '';
        $this->vincular_empleado_encontrado = null;
        $this->modalVincular = true;
    }

    public function cerrarVincular()
    {
        $this->modalVincular = false;
        $this->reset(['vincular_usuario_id', 'vincular_cedula_buscar', 'vincular_empleado_encontrado']);
    }

    public function buscarEmpleado()
    {
        $this->validate([
            'vincular_cedula_buscar' => 'required|string|min:4',
        ], [
            'vincular_cedula_buscar.required' => 'Ingrese una cédula para buscar.',
            'vincular_cedula_buscar.min'      => 'Ingrese al menos 4 caracteres.',
        ]);

        $cedula = trim($this->vincular_cedula_buscar);

        $empleado = Empleado::where('documento', 'LIKE', '%' . $cedula . '%')
            ->whereDoesntHave('user')
            ->first();

        if (!$empleado) {
            $this->vincular_empleado_encontrado = null;
            $this->dispatch('alertError', message: 'No se encontró un empleado con esa cédula o ya está vinculado a otro usuario.');
            return;
        }

        $this->vincular_empleado_encontrado = $empleado;
    }

    public function confirmarVinculacion()
    {
        if (!$this->vincular_empleado_encontrado || !$this->vincular_usuario_id) {
            $this->dispatch('alertError', message: 'Datos incompletos para la vinculación.');
            return;
        }

        $usuario = User::find($this->vincular_usuario_id);
        $empleado = Empleado::find($this->vincular_empleado_encontrado->empleado_id);

        if (User::where('empleado_id', $empleado->empleado_id)->exists()) {
            $this->dispatch('alertError', message: 'Este empleado ya está vinculado a otro usuario.');
            return;
        }

        $usuario->empleado_id = $empleado->empleado_id;
        $usuario->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se vinculó el empleado ({$empleado->empleado_id}) al usuario ({$usuario->id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Empleado vinculado correctamente al usuario.');
        $this->cerrarVincular();
    }

    // ==========================================
    // TAB INTEGRANTES — EDITAR USUARIO
    // ==========================================
    public function editUsuario(User $usuario)
    {
        $this->resetValidation();
        $this->editForm->edit($usuario);
    }

    public function updateUsuario()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Usuario editado exitosamente!');
    }

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

    public function desactivarUsuario(User $usuario)
    {
        $usuario->activo = false;
        $usuario->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el usuario ({$usuario->id}) desde Mi Oficina ({$this->oficina_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Usuario Desactivado exitosamente!');
    }

    public function activarUsuario(User $usuario)
    {
        $usuario->activo = true;
        $usuario->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó el usuario ({$usuario->id}) desde Mi Oficina ({$this->oficina_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Usuario Activado exitosamente!');
    }

    // ==========================================
    // TAB VEHÍCULOS — EDITAR
    // ==========================================
    public function editVehiculo(Vehiculo $vehiculo)
    {
        $this->resetValidation();
        $this->editFormVehiculo->edit($vehiculo);
    }

    public function updateVehiculo()
    {
        $this->editFormVehiculo->update();
        $this->dispatch('alertSuccess', message: 'Vehiculo editado exitosamente!');
    }

    public function editChofer(Vehiculo $vehiculo)
    {
        $this->resetValidation();
        $this->editFormChofer->edit($vehiculo);
    }

    public function updateChofer()
    {
        $this->editFormChofer->update();
        $this->dispatch('alertSuccess', message: 'Chofer Asignado exitosamente!');
    }

    public function desactivarVehiculo(Vehiculo $vehiculo)
    {
        $vehiculo->Activo = false;
        $vehiculo->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el vehículo ({$vehiculo->vehiculo_id}) desde Mi Oficina ({$this->oficina_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Vehiculo Desactivado exitosamente!');
    }

    public function activarVehiculo(Vehiculo $vehiculo)
    {
        $vehiculo->Activo = true;
        $vehiculo->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó el vehículo ({$vehiculo->vehiculo_id}) desde Mi Oficina ({$this->oficina_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Vehiculo Activado exitosamente!');
    }

    // ==========================================
    // TAB VEHÍCULOS — MANTENIMIENTO
    // ==========================================
    public function createMantenimiento(Vehiculo $vehiculo)
    {
        $this->resetValidation();
        $this->CreateForm3Vehiculo->create($vehiculo);
    }

    public function storeMantenimiento()
    {
        $this->CreateForm3Vehiculo->store();
        $this->dispatch('alertSuccess', message: 'Viajes creados exitosamente!');
    }

    public function addServicio()
    {
        $this->servicios[] = ['servicio_id' => '', 'fecha' => ''];
    }

    public function removeServicio($index)
    {
        unset($this->servicios[$index]);
        $this->servicios = array_values($this->servicios);
    }

    // ==========================================
    // TAB VEHÍCULOS — COMBUSTIBLE
    // ==========================================
    public function openCombustibleModal($vehiculo_id)
    {
        $this->combustibleForm = [
            'vehiculo_id' => $vehiculo_id,
            'fecha' => date('Y-m-d'),
            'litros' => '',
            'costo_total' => '',
            'kilometraje' => '',
        ];
        $this->combustibleModal['open'] = true;
    }

    public function saveCombustible()
    {
        $this->validate([
            'combustibleForm.fecha' => 'required|date',
            'combustibleForm.litros' => 'required|numeric|min:0',
            'combustibleForm.costo_total' => 'required|numeric|min:0',
            'combustibleForm.kilometraje' => 'required|integer|min:0',
        ], [
            'combustibleForm.fecha.required' => 'La fecha es obligatoria.',
            'combustibleForm.litros.required' => 'Los litros son obligatorios.',
            'combustibleForm.costo_total.required' => 'El costo es obligatorio.',
            'combustibleForm.kilometraje.required' => 'El kilometraje es obligatorio.',
        ]);

        \App\Models\CargaCombustible::create([
            'vehiculo_id' => $this->combustibleForm['vehiculo_id'],
            'fecha' => $this->combustibleForm['fecha'],
            'litros' => $this->combustibleForm['litros'],
            'costo_total' => $this->combustibleForm['costo_total'],
            'kilometraje' => $this->combustibleForm['kilometraje'],
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se registró carga de combustible del vehículo ({$this->combustibleForm['vehiculo_id']}) con {$this->combustibleForm['litros']} litros desde Mi Oficina ({$this->oficina_id})",
        ]);

        $this->combustibleModal['open'] = false;
        $this->dispatch('alertSuccess', message: 'Carga de combustible registrada exitosamente!');
    }

    // ==========================================
    // TAB ROLES
    // ==========================================
    public function createRol()
    {
        $this->resetValidation();
        $this->CreateForm3Rol->create($this->oficina_id);
    }

    public function storeRol()
    {
        $this->CreateForm3Rol->store();
        $this->dispatch('alertSuccess', message: 'Rol Agregado Exitosamente!');
        $this->dispatch('tarifaUpdated');
    }

    public function editRolCantidad($id, $currentCantidadMax)
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

        $this->editingId = null;
        $this->cantidadMaxEdit = null;
        $this->dispatch('alertSuccess', message: 'Cantidad máxima actualizada exitosamente.');
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->cantidadMaxEdit = null;
    }

    #[On('deleteRol')]
    public function deleteRol(OficinaPersonal $rol)
    {
        $rol_id = $rol->oficina_personal_id ?? $rol->getKey();
        $rol_nombre = optional(Role::find($rol->rol_id))->name ?? 'desconocido';

        $rol->delete();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'delete',
            'descripcion' => "Se eliminó el rol '{$rol_nombre}' ({$rol_id}) de la oficina ({$this->oficina_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Rol eliminado exitosamente!');
        $this->dispatch('tarifaUpdated');
    }

    // ==========================================
    // RENDER
    // ==========================================
    public function render()
    {
        // Queries comunes
        $usuarioIds = UsuarioEstado::where('id_estado', $this->oficina->estado_id)->pluck('id_user');

        $usuarios = User::with('roles')
            ->whereIn('id', $usuarioIds)
            ->whereHas('roles', function ($query) {
                $query->where('id', 3);
            })
            ->whereNull('oficina_id')
            ->get();

        $rolescantidad = OficinaPersonal::where('oficina_id', $this->oficina_id)->get();
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
        $tiposvehiculos = TipoVehiculo::select('tipo_vehiculo_id', 'tipo')->orderBy('tipo', 'asc')->get();

        $empleadosDisponibles = Empleado::where('oficina_id', $this->oficina_id)
            ->whereDoesntHave('user')
            ->orderBy('nombre')
            ->get();

        $usuariosSinEmpleado = User::where('oficina_id', $this->oficina_id)
            ->whereNull('empleado_id')
            ->orderBy('name')
            ->get();

        $data = [
            'oficina' => $this->oficina,
            'usuarios' => $usuarios,
            'oficinausuarios' => $oficinausuarios,
            'rolescantidad' => $rolescantidad,
            'rolesNombres' => $rolesNombres,
            'cantidad' => $cantidad,
            'rolesCount' => $rolesCount,
            'rolChofer' => $rolChofer,
            'tiposvehiculos' => $tiposvehiculos,
            'empleadosDisponibles' => $empleadosDisponibles,
            'usuariosSinEmpleado' => $usuariosSinEmpleado,
            'tiposDocumento' => Documento::whereIn('tipo', ['V', 'E'])->get(),
        ];

        // Datos específicos por tab
        if ($this->tab === 'integrantes') {
            $data['usuariosSinRol'] = User::where('oficina_id', $this->oficina_id)
                ->whereDoesntHave('roles')
                ->where('activo', true)
                ->orderBy('name')
                ->get();
        }

        if ($this->tab === 'vehiculos') {
            $data['vehiculos'] = Vehiculo::when($this->oficina_id, function ($query) {
                    $query->where('oficina_id', $this->oficina_id);
                })
                ->when($this->search, function ($query) {
                    $query->where('placa', 'LIKE', '%' . $this->search . '%');
                })
                ->orderBy($this->sortBy, $this->sortDir)
                ->paginate($this->perPage);

            $data['choferes'] = User::where('oficina_id', $this->oficina_id)
                ->where('activo', true)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Chofer');
                })
                ->get();

            $this->serviciosActivos = ServicioFlota::where('activo', true)->get();
            $data['serviciosActivos'] = $this->serviciosActivos;
        }

        if ($this->tab === 'roles') {
            // Refresh roles data
            $assignedRoles = OficinaPersonal::where('oficina_id', $this->oficina_id)
                ->pluck('rol_id')
                ->toArray();
            $this->rolesOficina = Role::whereNotIn('id', array_merge($assignedRoles, [1, 2, 3, 17, 38, 39, 40, 41, 42]))->get();
            $this->personalOficina = OficinaPersonal::where('oficina_id', $this->oficina_id)->get();

            $data['roleOficina'] = $this->rolesOficina;
            $data['rolesTabla'] = $this->personalOficina;
        }

        return view('livewire.oficinas.mi-oficina', $data);
    }
}
