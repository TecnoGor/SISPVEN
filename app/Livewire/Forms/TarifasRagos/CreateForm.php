<?php

namespace App\Livewire\Forms\TarifasRagos;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use App\Models\TarifaNacionalRango;

class CreateForm extends Form
{
    public $open = false;
    public $servicio_id;

    #[Validate('required|numeric|min:1|unique:'.TarifaNacionalRango::class)]
    public $desde;

    #[Validate('required|numeric|min:1|unique:'.TarifaNacionalRango::class)]
    public $hasta;

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
        $medidaIds = TarifaNacionalRango::where('servicios_id', $servicio_id_new )
        ->pluck('medida_id');

        $medida_id = $medidaIds->first();

        $this->validate();

        $desde = strtoupper($this->desde);
        $hasta = strtoupper($this->hasta);
        $monto = strtoupper($this->monto);

        $tarifa = TarifaNacionalRango::create(['desde' => $desde, 'hasta' => $hasta, 'monto' => $monto, 'activo' => true, 'medida_id' => $medida_id, 'servicios_id' => $servicio_id_new]);

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'update',
            'descripcion' => "Usuario {$usuario->id} hizo un create de una tarifa tipo rango"
        ]);
        $this->reset();
        $this->open = false;
    }
    
}