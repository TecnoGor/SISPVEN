<?php

namespace App\Livewire\Forms\ServiciosFlota;

use Livewire\Form;
use App\Models\ServicioFlota;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class CreateForm extends Form
{
    public $open = false;

    #[Validate('required|string|min:3|max:20|unique:'.ServicioFlota::class)]
    public $nombre;

    public function create()
    {
        $this->open = true;
    }

    public function store()
    {
        $this->validate();

        $nombre = $this->nombre;

        $servicio = ServicioFlota::create(['nombre' => $nombre]);

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'create',
            'descripcion' => "usuario $usuario->id hizo un create del servicio: $servicio->servicios_flota_id"
        ]);
        $this->reset();
        $this->open = false;
    }
}  
