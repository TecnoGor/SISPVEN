<?php

namespace App\Livewire\Forms\TarifasRagosInternacional;

use App\Models\TarifaInternacionalRango;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\UsuarioSeguimiento;

class EditForm extends Form
{
    public $tarifa_id = '';
    public $open = false;

    #[Validate('required|numeric|min:1')]
    public $desde;

    #[Validate('required|numeric|min:1')]
    public $hasta;

    #[Validate('required|numeric|min:1')]
    public $monto;

    public function edit(TarifaInternacionalRango $tarifa)
    {
        $this->open = true;
        $this->tarifa_id = $tarifa->tarifa_internacional_rango_id;
        $this->desde = $tarifa->desde;
        $this->hasta = $tarifa->hasta;
        $this->monto = $tarifa->monto;
    }

    public function update()
    {
        $this->validate();

        $tarifa = TarifaInternacionalRango::find($this->tarifa_id);

        $tarifa->update(
            $this->all()
        );

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'update',
            'descripcion' => "Usuario {$usuario->id} hizo un update de una tarifa tipo rango"
        ]);
        
        // Cambiar el estado de la propiedad open
        $this->open = false;
    }
}
