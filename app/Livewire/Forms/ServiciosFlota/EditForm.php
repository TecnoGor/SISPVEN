<?php

namespace App\Livewire\Forms\ServiciosFlota;

use Livewire\Form;
use App\Models\ServicioFlota;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm extends Form
{
    public $servicios_flota_id = '';
    public $open = false;

    #[Validate('required|string|min:3|max:20')]
    public $nombre;

    

    public function edit(ServicioFlota $servicio)
    {
        $this->open = true;
        $this->servicios_flota_id = $servicio->servicios_flota_id;

        $this->nombre = $servicio->nombre;
    }

    public function update()
    {
        $this->validate();

        $servicio = ServicioFlota::find($this->servicios_flota_id);

        $servicio->update(
            $this->all()
        );

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'update',
            'descripcion' => "Usuario {$usuario->id} hizo un update del servicio de flota: {$servicio->servicios_flota_id}"
        ]);
        
        
        // Cambiar el estado de la propiedad open
        $this->open = false;
    }
    
}
