<?php

namespace App\Livewire\Oficinas;

use App\Models\User;
use App\Models\Estado;
use App\Models\Documento;
use App\Models\Empleado;
use App\Models\Oficina;
use App\Models\Vehiculo;
use Livewire\Component;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\TipoVehiculo;
use App\Models\UsuarioEstado;
use App\Models\UsuarioSeguimiento;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\OficinaPersonal;
use App\Models\ServicioFlota;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Role;
use App\Livewire\Forms\OficinaDetalles\CreateForm;
use App\Livewire\Forms\OficinaDetalles\CreateForm2;
use App\Livewire\Forms\OficinaDetalles\CreateForm3;
use App\Livewire\Forms\OficinaDetalles\DeleteForm;
use App\Livewire\Forms\Usuarios\EditForm1;
use App\Livewire\Forms\Vehiculos\EditForm as VehiculoEditForm;
use App\Livewire\Forms\Vehiculos\EditForm3 as VehiculoEditForm3;
use App\Livewire\Forms\Vehiculos\CreateForm3 as VehiculoCreateForm3;

#[Layout('layouts.app')]
class Detalles extends Component
{
    use WithFileUploads, WithPagination;

    // — Oficina base —
    public $oficina_id;
    public $oficina;
    public $estadoNombre;
    public $municipioNombre;
    public $parroquiaNombre;
    public $tab = 'integrantes';
    public $mod = false;
    public $modificar_oficina_id;

    // — Form objects —
    public EditForm1 $editForm;
    public CreateForm $CreateForm;
    public CreateForm2 $CreateForm2;
    public CreateForm3 $CreateForm3;
    public DeleteForm $deleteForm;
    public VehiculoEditForm $editFormVehiculo;
    public VehiculoEditForm3 $editFormChofer;
    public VehiculoCreateForm3 $createFormMantenimiento;

    // — Integrantes —
    public $rol_asignar = [];

    // — Vehículos —
    public $search;
    public $perPage = 5;
    public $sortBy = 'vehiculo_id';
    public $sortDir = 'ASC';
    public $serviciosActivos;
    public $servicios = [['servicio_id' => '', 'fecha' => '']];

    public $combustibleModal = ['open' => false];
    public $combustibleForm = [
        'vehiculo_id' => null,
        'fecha' => '',
        'litros' => '',
        'costo_total' => '',
        'kilometraje' => '',
    ];

    public $mantenimientoModal = ['open' => false];
    public $mantenimientoForm = [
        'vehiculo_id' => null,
        'descripcion' => '',
        'fecha' => '',
        'kilometraje' => '',
        'costo' => '',
    ];

    // — Roles —
    public $rolesOficina;
    public $personalOficina;
    public $editingId = null;
    public $cantidadMaxEdit;

    public function mount($oficina_id)
    {
        $this->oficina_id = $oficina_id;
        $this->oficina    = Oficina::find($this->oficina_id);

        if ($this->oficina) {
            $estado = Estado::find($this->oficina->estado_id);
            $this->estadoNombre = $estado ? $estado->nombre : 'Desconocido';
            $municipio = Municipio::find($this->oficina->municipio_id);
            $this->municipioNombre = $municipio ? $municipio->nombre : 'Desconocido';
            $parroquia = Parroquia::find($this->oficina->parroquia_id);
            $this->parroquiaNombre = $parroquia ? $parroquia->nombre : 'Desconocido';
        }

        $assigned = OficinaPersonal::where('oficina_id', $oficina_id)->pluck('rol_id')->toArray();
        $this->rolesOficina  = Role::whereNotIn('id', array_merge($assigned, [1, 2, 3, 17, 38, 39, 40, 41, 42]))->get();
        $this->personalOficina = OficinaPersonal::where('oficina_id', $this->oficina_id)->get();
    }

    // ─── Oficina info ────────────────────────────────────────────────────────

    public function setTab(string $tab)
    {
        $this->tab = $tab;
    }

    public function modificar($id)
    {
        $this->modificar_oficina_id = $id;
        $this->mod = true;
    }

    // ─── Integrantes ─────────────────────────────────────────────────────────

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
                    ->where('rol_id', $rolSeleccionado->id)->first();
                if ($configuracionRol) {
                    $usuariosConRol = User::where('oficina_id', $this->oficina_id)
                        ->whereHas('roles', fn($q) => $q->where('id', $rolSeleccionado->id))
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

    public function asignarRol($userId)
    {
        $rolId   = $this->rol_asignar[$userId] ?? null;
        if (!$rolId) {
            $this->dispatch('alertError', message: 'Debe seleccionar un rol.');
            return;
        }

        $usuario = User::find($userId);
        $rol     = Role::find($rolId);

        if (!$usuario || !$rol) {
            $this->dispatch('alertError', message: 'Usuario o rol no válido.');
            return;
        }

        $config = OficinaPersonal::where('oficina_id', $this->oficina_id)
            ->where('rol_id', $rol->id)->first();

        if ($config) {
            $count = User::where('oficina_id', $this->oficina_id)
                ->whereHas('roles', fn($q) => $q->where('id', $rol->id))->count();
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

    // ─── Vehículos ───────────────────────────────────────────────────────────

    public function create2()
    {
        $this->resetValidation();
        $this->CreateForm2->create($this->oficina_id);
    }

    public function store2()
    {
        $this->CreateForm2->store();
        $this->dispatch('alertSuccess', message: 'Vehículo Registrado Exitosamente!');
    }

    public function editVehiculo(Vehiculo $vehiculo)
    {
        $this->resetValidation();
        $this->editFormVehiculo->edit($vehiculo);
    }

    public function updateVehiculo()
    {
        $this->editFormVehiculo->update();
        $this->dispatch('alertSuccess', message: 'Vehículo editado exitosamente!');
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

    public function create3(Vehiculo $vehiculo)
    {
        $this->resetValidation();
        $this->createFormMantenimiento->create($vehiculo);
    }

    public function store3()
    {
        $this->createFormMantenimiento->store();
        $this->dispatch('alertSuccess', message: 'Viajes creados exitosamente!');
    }

    public function desactivarVehiculo(Vehiculo $vehiculo)
    {
        $vehiculo->Activo = false;
        $vehiculo->save();
        $this->dispatch('alertSuccess', message: 'Vehículo Desactivado exitosamente!');
    }

    public function activarVehiculo(Vehiculo $vehiculo)
    {
        $vehiculo->Activo = true;
        $vehiculo->save();
        $this->dispatch('alertSuccess', message: 'Vehículo Activado exitosamente!');
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

    public function openCombustibleModal($vehiculo_id)
    {
        $this->combustibleForm = [
            'vehiculo_id' => $vehiculo_id,
            'fecha'        => date('Y-m-d'),
            'litros'       => '',
            'costo_total'  => '',
            'kilometraje'  => '',
        ];
        $this->combustibleModal['open'] = true;
    }

    public function saveCombustible()
    {
        $this->validate([
            'combustibleForm.fecha'        => 'required|date',
            'combustibleForm.litros'       => 'required|numeric|min:0',
            'combustibleForm.costo_total'  => 'required|numeric|min:0',
            'combustibleForm.kilometraje'  => 'required|integer|min:0',
        ]);

        \App\Models\CargaCombustible::create([
            'vehiculo_id' => $this->combustibleForm['vehiculo_id'],
            'fecha'        => $this->combustibleForm['fecha'],
            'litros'       => $this->combustibleForm['litros'],
            'costo_total'  => $this->combustibleForm['costo_total'],
            'kilometraje'  => $this->combustibleForm['kilometraje'],
        ]);

        $this->combustibleModal['open'] = false;
        $this->dispatch('alertSuccess', message: 'Carga de combustible registrada exitosamente!');
    }

    public function openMantenimientoModal($vehiculo_id)
    {
        $this->mantenimientoForm = [
            'vehiculo_id' => $vehiculo_id,
            'descripcion'  => '',
            'fecha'        => date('Y-m-d'),
            'kilometraje'  => '',
            'costo'        => '',
        ];
        $this->mantenimientoModal['open'] = true;
    }

    public function saveMantenimiento()
    {
        $this->validate([
            'mantenimientoForm.descripcion' => 'required|string',
            'mantenimientoForm.fecha'       => 'required|date',
            'mantenimientoForm.kilometraje' => 'required|integer|min:0',
            'mantenimientoForm.costo'       => 'required|numeric|min:0',
        ]);

        \App\Models\Mantenimiento::create([
            'vehiculo_id' => $this->mantenimientoForm['vehiculo_id'],
            'descripcion'  => $this->mantenimientoForm['descripcion'],
            'fecha'        => $this->mantenimientoForm['fecha'],
            'kilometraje'  => $this->mantenimientoForm['kilometraje'],
            'costo'        => $this->mantenimientoForm['costo'],
        ]);

        $this->mantenimientoModal['open'] = false;
        $this->dispatch('alertSuccess', message: 'Mantenimiento registrado exitosamente!');
    }

    // ─── Roles ───────────────────────────────────────────────────────────────

    public function createRol()
    {
        $this->resetValidation();
        $this->CreateForm3->create($this->oficina_id);
    }

    public function storeRol()
    {
        $this->CreateForm3->store();
        $this->dispatch('alertSuccess', message: 'Rol Agregado Exitosamente!');
        $this->dispatch('tarifaUpdated');
    }

    public function edit2($id, $currentCantidadMax)
    {
        $this->editingId       = $id;
        $this->cantidadMaxEdit = $currentCantidadMax;
    }

    public function updateCantidadMax($id)
    {
        $this->validate(['cantidadMaxEdit' => 'required|numeric|min:0']);
        $rol = OficinaPersonal::findOrFail($id);
        $rol->update(['cantidad_max' => $this->cantidadMaxEdit]);
        $this->editingId       = null;
        $this->cantidadMaxEdit = null;
        $this->dispatch('alertSuccess', message: 'Cantidad máxima actualizada exitosamente.');
    }

    public function cancelEdit()
    {
        $this->editingId       = null;
        $this->cantidadMaxEdit = null;
    }

    #[On('delete')]
    public function deleteRol(OficinaPersonal $rol)
    {
        $rol->delete();
        $this->personalOficina = OficinaPersonal::where('oficina_id', $this->oficina_id)->get();
        $this->dispatch('alertSuccess', message: 'Rol eliminado exitosamente!');
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    public function render()
    {
        $usuarioIds = UsuarioEstado::where('id_estado', $this->oficina->estado_id)->pluck('id_user');

        $usuarios = User::with('roles')
            ->whereIn('id', $usuarioIds)
            ->whereHas('roles', fn($q) => $q->where('id', 3))
            ->whereNull('oficina_id')
            ->get();

        $rolescantidad = OficinaPersonal::where('oficina_id', $this->oficina_id)->get();
        $rolesIds      = $rolescantidad->pluck('rol_id')->unique();
        $roles         = Role::whereIn('id', $rolesIds)->get(['id', 'name']);
        $rolesNombres  = $roles->pluck('name', 'id');

        $oficinausuarios = User::where('oficina_id', $this->oficina_id)->get();

        $rolesCount = [];
        foreach ($roles as $rol) {
            $rolesCount[$rol->id] = User::where('oficina_id', $this->oficina_id)
                ->whereHas('roles', fn($q) => $q->where('id', $rol->id))
                ->count();
        }

        $empleadosDisponibles = Empleado::where('oficina_id', $this->oficina_id)
            ->whereDoesntHave('user')->orderBy('nombre')->get();

        $usuariosSinRol = User::where('oficina_id', $this->oficina_id)
            ->whereDoesntHave('roles')->where('activo', true)->orderBy('name')->get();

        $vehiculos = Vehiculo::where('oficina_id', $this->oficina_id)
            ->when($this->search, fn($q) => $q->where('placa', 'LIKE', '%' . $this->search . '%'))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $choferes = User::where('oficina_id', $this->oficina_id)
            ->where('activo', true)
            ->whereHas('roles', fn($q) => $q->where('name', 'Chofer'))
            ->get();

        $this->serviciosActivos = ServicioFlota::where('activo', true)->get();
        $tiposvehiculos = TipoVehiculo::orderBy('tipo')->get();
        $rolChofer      = Role::where('name', 'Chofer')->first();

        $tiposDocumento = Documento::whereIn('tipo', ['V', 'E'])->get();

        return view('livewire.oficinas.detalles', [
            'oficina'              => $this->oficina,
            'usuarios'             => $usuarios,
            'oficinausuarios'      => $oficinausuarios,
            'rolescantidad'        => $rolescantidad,
            'rolesNombres'         => $rolesNombres,
            'rolesCount'           => $rolesCount,
            'empleadosDisponibles' => $empleadosDisponibles,
            'usuariosSinRol'       => $usuariosSinRol,
            'vehiculos'            => $vehiculos,
            'choferes'             => $choferes,
            'tiposvehiculos'       => $tiposvehiculos,
            'rolChofer'            => $rolChofer,
            'roleOficina'          => $this->rolesOficina,
            'roles'                => $this->personalOficina,
            'serviciosActivos'     => $this->serviciosActivos,
            'tiposDocumento'       => $tiposDocumento,
        ]);
    }
}
