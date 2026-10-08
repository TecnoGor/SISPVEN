<?php

namespace App\Livewire\Forms\OficinaDetalles;

use Livewire\Form;
use App\Models\OficinaPersonal;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class DeleteForm extends Form
{
    public function destroy(OficinaPersonal $rol)
    {
        $rol->delete();

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'delete',
            'descripcion' => "usuario $usuario->id hizo un delete del rol $rol->id"
        ]);
    }
}
