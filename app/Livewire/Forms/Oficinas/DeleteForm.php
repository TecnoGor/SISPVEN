<?php

namespace App\Livewire\Forms\Oficinas;

use App\Models\Oficina;
use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class DeleteForm extends Form
{
    public function destroy(Oficina $oficinas)
    {
        $oficinas->delete();

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'delete',
            'descripcion' => "usuario $usuario->id hizo un delete de la oficina $oficinas->oficina_id"
        ]);
    }
}
