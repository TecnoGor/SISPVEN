<?php

namespace App\Livewire\Forms\Choferes;

use Livewire\Form;
use App\Models\Chofer;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class CreateForm extends Form
{
    public $open = false;
    public $nombre;
    public $cedula;
    public $rif;
    public $direccion;
    public $telefono;
    public $correo;


    public function create()
    {
        $this->open = true;
    }

    public function store($proveedor_id)
    {
        
        $this->validate([
            'nombre' => 'required',
            'rif' => 'required',
            'cedula' => 'required|numeric',
            'direccion' => 'required',
            'telefono' => 'required|numeric',
            'correo' => 'required|email',
        ]);
    
        // Crear el vehículo con los datos ingresados
        $vehiculo = Chofer::create([
            'nombre' => $this->nombre,
            'cedula' => $this->cedula,
            'rif' => $this->rif,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'proveedor_id' => $proveedor_id,
            'activo' => true,
        ]);

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'create',
            'descripcion' => "Usuario {$usuario->id} creó un Chofer.",
        ]);

        $this->reset();
        $this->open = false;
    }
}
