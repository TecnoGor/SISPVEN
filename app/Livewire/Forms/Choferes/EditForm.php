<?php

namespace App\Livewire\Forms\Choferes;

use Livewire\Form;
use App\Models\Chofer;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm extends Form
{
    public $open = false;
    public $chofer_id = '';
    public $origen;
    public $nombre, $cedula, $rif, $direccion, $telefono, $correo;

    public function edit(Chofer $chofer)
    {
        $this->open = true;
        $this->chofer_id = $chofer->chofer_id;
        $this->nombre = $chofer->nombre;
        $this->cedula = $chofer->cedula;
        $this->rif = $chofer->rif;
        $this->direccion = $chofer->direccion;
        $this->telefono = $chofer->telefono;
        $this->correo = $chofer->correo;
    }

    public function update()
    {
        $chofer = Chofer::find($this->chofer_id);
        
        $chofer->nombre = $this->nombre;
        $chofer->cedula = $this->cedula;
        $chofer->rif = $this->rif;
        $chofer->direccion = $this->direccion;
        $chofer->telefono =$this->telefono;
        $chofer->correo = $this->correo;

        $chofer->save();

        $user = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $user->id,
            'accion' => 'update',
            'descripcion' => "Usuario $user->id hizo un update a un chofer"
        ]);

        $this->open = false;
    }
}
