<?php

namespace App\Livewire\Forms\RutasProveedor;

use Livewire\Form;
use App\Models\ProveedorRuta;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class CreateForm extends Form
{
    public $open = false;
    public $ruta_id;

    public function create()
    {
        $this->open = true;
    }

    public function store($proveedor_id)
    {
        // Validar la propiedad ruta_id
        $this->validate([
            'ruta_id' => 'required|exists:rutas,ruta_id',  // Actualiza aquí
        ], [
            'ruta_id.required' => 'Debes seleccionar una ruta.',  // Actualiza aquí
            'ruta_id.exists' => 'La ruta seleccionada no es válida.',  // Actualiza aquí
        ]);
        
        // Crear el registro en la tabla ProveedorRuta
        ProveedorRuta::create([
            'proveedor_id' => $proveedor_id,
            'ruta_id' => $this->ruta_id,
        ]);

        // Registrar la acción del usuario
        $usuario = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'create',
            'descripcion' => "Usuario {$usuario->id} asignó una ruta al proveedor con ID: {$proveedor_id}",
        ]);

        // Reiniciar el formulario y cerrar el modal
        $this->reset();
        $this->open = false;
    }
}
