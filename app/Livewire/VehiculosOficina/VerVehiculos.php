<?php

namespace App\Livewire\VehiculosOficina;

use App\Models\User;
use App\Models\Chofer;
use App\Models\Estado;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Vehiculo;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\ServicioFlota;
use App\Models\UsuarioEstado;
use Livewire\WithFileUploads;
use App\Models\VehiculoPropio;
use GuzzleHttp\Promise\Create;
use App\Models\OficinaPersonal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Role;
use App\Livewire\Forms\Vehiculos\EditForm;
use App\Livewire\Forms\Vehiculos\EditForm3;
use App\Livewire\Forms\Vehiculos\CreateForm3;
use App\Livewire\Forms\OficinaDetalles\CreateForm;
use App\Livewire\Forms\OficinaDetalles\CreateForm2;
use App\Models\TipoVehiculo;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')] 
class VerVehiculos extends Component
{
    use WithFileUploads;

        public $oficina_id;
        public $oficina;
        public $estadoNombre;
        public $municipioNombre;
        public $parroquiaNombre;
        public EditForm $editForm;
        public EditForm3 $editForm3;
        public CreateForm $CreateForm;
        public CreateForm2 $CreateForm2;
        public CreateForm3 $CreateForm3;
        public $search;
        public $filter = 'all'; 
        public $perPage = 5; 
        public $sortBy = 'vehiculo_id';
        public $sortDir = 'ASC';
        public $serviciosActivos;
        public $serviciosAdicionales = [];
        public $servicios = [
            ['servicio_id' => '', 'fecha' => '']
        ];
        public $descripcion = '';
        public $serviciosSeleccionados = [];
        #[Validate('image|max:1024')] // 1MB Max
        public $editMode = false;
        public $nombre;
        public $correo;
        public $telefono;
        #[Validate('image|max:1024')] // 1MB Max
        public $photo;
        private $originalValues = [];
        public $personalOficina;
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

    public function edit(Vehiculo $vehiculo)
    {
        $this->resetValidation();
        $this->editForm->edit($vehiculo);
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Vehiculo editado exitosamente!');
    }
    
    public function edit2(Vehiculo $vehiculo)
    {
        $this->resetValidation();
        $this->editForm3->edit($vehiculo);
    }

    public function update2()
    {
        $this->editForm3->update();
        $this->dispatch('alertSuccess', message: 'Chofer Asignado exitosamente!');
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

        public function addServicio()
        {
            $this->servicios[] = ['servicio_id' => '', 'fecha' => ''];
        }
        
        public function removeServicio($index)
        {
            unset($this->servicios[$index]);
            $this->servicios = array_values($this->servicios); 
        }

        public function create3(Vehiculo $vehiculo)
        {
            $this->resetValidation();  
            $this->CreateForm3->create($vehiculo);
        }
        
        public function store3()
        {
            $this->CreateForm3->store();
            $this->dispatch('alertSuccess', message: 'Viajes creados exitosamente!');
        }

        public function desactivar(Vehiculo $vehiculo)
        {
            $vehiculo->Activo = false;
            $vehiculo->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se desactivó el vehículo ({$vehiculo->vehiculo_id})",
            ]);

            $this->dispatch('alertSuccess', message: 'Vehiculo Desactivado exitosamente!');
        }
        public function activar(Vehiculo $vehiculo)
        {
            $vehiculo->Activo = true;
            $vehiculo->save();

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se activó el vehículo ({$vehiculo->vehiculo_id})",
            ]);

            $this->dispatch('alertSuccess', message: 'Vehiculo Activado exitosamente!');
        }

    public $combustibleModal = [
        'open' => false,
    ];
    public $combustibleForm = [
        'vehiculo_id' => null,
        'fecha' => '',
        'litros' => '',
        'costo_total' => '',
        'kilometraje' => '',
    ];

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
            'descripcion' => "Se registró carga de combustible del vehículo ({$this->combustibleForm['vehiculo_id']}) con {$this->combustibleForm['litros']} litros",
        ]);

        $this->combustibleModal['open'] = false;
        $this->dispatch('alertSuccess', message: 'Carga de combustible registrada exitosamente!');
    }

    public $mantenimientoModal = [
        'open' => false,
    ];
    public $mantenimientoForm = [
        'vehiculo_id' => null,
        'descripcion' => '',
        'fecha' => '',
        'kilometraje' => '',
        'costo' => '',
    ];

    public function openMantenimientoModal($vehiculo_id)
    {
        $this->mantenimientoForm = [
            'vehiculo_id' => $vehiculo_id,
            'descripcion' => '',
            'fecha' => date('Y-m-d'),
            'kilometraje' => '',
            'costo' => '',
        ];
        $this->mantenimientoModal['open'] = true;
    }

    public function saveMantenimiento()
    {
        $this->validate([
            'mantenimientoForm.descripcion' => 'required|string',
            'mantenimientoForm.fecha' => 'required|date',
            'mantenimientoForm.kilometraje' => 'required|integer|min:0',
            'mantenimientoForm.costo' => 'required|numeric|min:0',
        ], [
            'mantenimientoForm.descripcion.required' => 'La descripción es obligatoria.',
            'mantenimientoForm.fecha.required' => 'La fecha es obligatoria.',
            'mantenimientoForm.kilometraje.required' => 'El kilometraje es obligatorio.',
            'mantenimientoForm.costo.required' => 'El costo es obligatorio.',
        ]);

        \App\Models\Mantenimiento::create([
            'vehiculo_id' => $this->mantenimientoForm['vehiculo_id'],
            'descripcion' => $this->mantenimientoForm['descripcion'],
            'fecha' => $this->mantenimientoForm['fecha'],
            'kilometraje' => $this->mantenimientoForm['kilometraje'],
            'costo' => $this->mantenimientoForm['costo'],
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se registró mantenimiento del vehículo ({$this->mantenimientoForm['vehiculo_id']})",
        ]);

        $this->mantenimientoModal['open'] = false;
        $this->dispatch('alertSuccess', message: 'Mantenimiento registrado exitosamente!');
    }


    public function render()
    {

        $usuarioIds = UsuarioEstado::where('id_estado', $this->oficina->estado_id)->pluck('id_user');

        $usuarios = User::with('roles')
        ->whereIn('id', $usuarioIds)
        ->whereHas('roles', function($query) {
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
                ->whereHas('roles', function($query) use ($rol) {
                    $query->where('id', $rol->id);
                })
                ->count();
                $rolesCount[$rol->id] = $count;
        
            }     
        
            $vehiculos = Vehiculo::when($this->oficina_id, function($query) {
                // Filtrar por proveedor_id
                $query->where('oficina_id', $this->oficina_id);
            })
            ->when($this->search, function($query) {
                // Filtrar por placa, como antes
                $query->where('placa', 'LIKE', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage); 

            $rolChofer = Role::where('name', 'Chofer')->first();

            $choferes = User::where('oficina_id', $this->oficina_id)
            ->where('activo', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Chofer');
            })
            ->get();
        
            $this->serviciosActivos = ServicioFlota::where('activo', true)->get();
        $tiposvehiculos = TipoVehiculo::all();

        return view('livewire.vehiculos-oficina.ver-vehiculos', [
            'vehiculos' => $vehiculos,
            'oficina' => $this->oficina,
            'usuarios' => $usuarios,
            'oficinausuarios' => $oficinausuarios,
            'rolescantidad' => $rolescantidad,
            'rolesNombres' => $rolesNombres,
            'cantidad' => $cantidad,
            'rolesCount' => $rolesCount,
            'rolChofer' => $rolChofer,
            'choferes' => $choferes,
            'tipos' =>  $tiposvehiculos,
            'serviciosActivos' => $this->serviciosActivos
        ]);
    }
}
