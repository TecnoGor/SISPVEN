<?php

namespace App\Livewire\Forms\TarifasRagosInternacional;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use App\Models\TarifaInternacionalConcepto;
use App\Models\TarifaInternacionalConceptoSeguimiento;

class CreateForm2 extends Form
{
    public $open = false;
    public $servicio_id;

    #[Validate('required|string|min:1|max:38|unique:'.TarifaInternacionalConcepto::class)]
    public $nombre;

    #[Validate('required|numeric|min:1')]
    public $monto;

    public function create($servicio_id)
    {
        $this->servicio_id = $servicio_id;
        $this->open = true;
    }

    public function store()
    {
        $servicio_id_new = $this->servicio_id;
        $this->validate();

        $nombre = strtoupper($this->nombre);
        $monto = strtoupper($this->monto);

        $tarifa = TarifaInternacionalConcepto::create(['nombre' => $nombre, 'monto' => $monto, 'activo' => true, 'servicios_id' => $servicio_id_new]);

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'update',
            'descripcion' => "Usuario {$usuario->id} hizo un create de una tarifa tipo Concepto"
        ]);
        
        $this->reset();
        $this->open = false;
    }
}
