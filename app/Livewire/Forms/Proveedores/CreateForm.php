<?php

namespace App\Livewire\Forms\Proveedores;

use App\Models\Proveedor;
use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use Spatie\Permission\Models\Role;

class CreateForm extends Form
{
public $open = false;
public $representante;
public $telefono;
public $cedula;
#[Validate('required|string|min:10|max:32|unique:proveedores,correo')]
public $correo;
public $rif;
public $rsocial;
public $direccion;
public $retencion;
public $contribuyente;

public function create()
{
    $this->open = true;
}

public function store()
{
    // Validar los datos antes de crear el proveedor
    $this->validate([
        'correo' => 'required|email',
    ]);

    // Crear el proveedor con los datos ingresados
    $proveedor = Proveedor::create([
        'representante_legal' => $this->representante,
        'telefono' => $this->telefono,
        'cedula' => $this->cedula,
        'correo' => $this->correo,
        'rif' => $this->rif,
        'razon_social' => $this->rsocial,
        'direccion_fiscal' => $this->direccion,
        'retencion' => $this->retencion,
        'contribuyente_especial' => $this->contribuyente,
    ]);
        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'create',
            'descripcion' => "usuario $usuario->id hizo un create de un Proveedor"
        ]);
        $this->reset();
        $this->open = false;
    }
}
