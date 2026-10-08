<?php

namespace App\Livewire\Forms\Proveedores;

use Livewire\Form;
use App\Models\Proveedor;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm extends Form
{
    public $proveedor_id = '';
    public $open = false;

    #[Validate('required')]
    public $representante_legal;

    #[Validate('required')]
    public $cedula;

    #[Validate('required')]
    public $telefono;

    #[Validate('required')]
    public $correo;

    #[Validate('required')]
    public $rif;

    #[Validate('required')]
    public $razon_social;

    #[Validate('required')]
    public $direccion_fiscal;
    
    public $retencion, $contribuyente_especial;

    public function edit(Proveedor $proveedor)
    {
        $this->open = true;
        $this->proveedor_id = $proveedor->proveedor_id;
        $this->representante_legal = $proveedor->representante_legal;
        $this->cedula = $proveedor->cedula;
        $this->telefono = $proveedor->telefono;
        $this->correo = $proveedor->correo;
        $this->rif = $proveedor->rif;
        $this->razon_social = $proveedor->razon_social;
        $this->direccion_fiscal = $proveedor->direccion_fiscal;
        $this->retencion = $proveedor->retencion;
        $this->contribuyente_especial = $proveedor->contribuyente_especial;
        
    }

    public function update()
    {
        $this->validate();

    $proveedor = Proveedor::find($this->proveedor_id);

        $proveedor->update(
            $this->all()
        );

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'update',
            'descripcion' => "Usuario {$usuario->id} hizo un update de un Proveedor"
        ]);
        
        // Cambiar el estado de la propiedad open
        $this->open = false;
    }
}
