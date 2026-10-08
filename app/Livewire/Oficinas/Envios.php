<?php

namespace App\Livewire\Oficinas;

use App\Models\User;
use App\Models\Envio;
use App\Models\Estado;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\UsuarioEstado;
use GuzzleHttp\Promise\Create;
use App\Models\OficinaPersonal;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use App\Livewire\Forms\OficinaDetalles\EditForm;
use App\Livewire\Forms\OficinaDetalles\CreateForm;

#[Layout('layouts.app')] 
class Envios extends Component
{
    public $oficina_id;
    public $oficina;
    public $estadoNombre;
    public $municipioNombre;
    public $parroquiaNombre;
    public EditForm $EditForm;
    public CreateForm $CreateForm;
    public $tipo_consulta, $codigo, $envio, $desde, $hasta, $buscar, $search;


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

    public function store()
    {
        $usuariosEnOficina = User::where('oficina_id', $this->oficina_id)->get();
        $rolesIds = $usuariosEnOficina->pluck('roles.*.id')->flatten()->unique();
        $rolescantidad = OficinaPersonal::where('oficina_id', $this->oficina_id)->first(); // Asegúrate de obtener el registro correcto
        $roles = Role::whereIn('id', $rolesIds)->get(['id', 'name']);
        $rolesCount = []; 

        foreach ($roles as $rol)
        {
        $count = User::where('oficina_id', $this->oficina_id)
            ->whereHas('roles', function ($query) use ($rol) {
                $query->where('id', $rol->id);
            })
                ->count();
                $rolesCount[$rol->id] = $count;
        }
                $rolSeleccionado = Role::find($this->CreateForm->role_id);

                if ($rolSeleccionado && (($rolesCount[$rolSeleccionado->id] ?? 0) >= $rolescantidad->cantidad_max)) {
                    session()->flash('error', 'No se puede registrar un nuevo usuario. Se ha alcanzado el límite máximo para este rol.');
                    return;
                }
                    $this->CreateForm->store();
                    $this->dispatch('alertSuccess', message: 'Integrante Creado Exitosamente!');
    }

    public function render()
    {
        $this->envio = Envio::where('oficina_id', $this->oficina_id)
        ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
        ->get();
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

        return view('livewire.oficinas.envios', [
            'oficina' => $this->oficina,
            'usuarios' => $usuarios,
            'oficinausuarios' => $oficinausuarios,
            'rolescantidad' => $rolescantidad,
            'rolesNombres' => $rolesNombres,
            'cantidad' => $cantidad,
            'rolesCount' => $rolesCount,

        ]);
    }

}
