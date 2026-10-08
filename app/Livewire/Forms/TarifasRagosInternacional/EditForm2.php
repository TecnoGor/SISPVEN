<?php

namespace App\Livewire\Forms\TarifasRagosInternacional;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use App\Models\TarifaInternacionalConcepto;
use App\Models\TarifaInternacionalConceptoSeguimiento;

class EditForm2 extends Form
{
    public $tarifa_id = '';
    public $open = false;

    #[Validate('required|string|min:1|max:255')]
    public $nombre;

    #[Validate('required|numeric|min:1')]
    public $monto;

    public function editconcep(TarifaInternacionalConcepto $tarifa)
    {
        $this->open = true;
        $this->tarifa_id = $tarifa->tarifa_conceptos_internacional_id;

        $this->nombre = $tarifa->nombre;
        $this->monto = $tarifa->monto;
    }

    public function updateconcep()
    {
        $this->validate();

        $tarifa = TarifaInternacionalConcepto::find($this->tarifa_id);

        $tarifa->update(
            $this->all()
        );

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'update',
            'descripcion' => "Usuario {$usuario->id} hizo un update de una tarifa tipo concepto"
        ]);
        
        TarifaInternacionalConceptoSeguimiento::create([
            'usuario_seguimiento_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'tarifa_conceptos_internacional_id' => $this->tarifa_id,

        ]);
        // Cambiar el estado de la propiedad open
        $this->open = false;
    }
}
