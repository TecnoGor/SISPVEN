<?php

namespace App\Livewire\Forms\TarifasRagos;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use App\Models\TarifaExpresoBolivariano;

class CreateForm3 extends Form
{
    public $open = false;
    public $servicio_id;

    #[Validate('required|numeric|min:1')]
    public $desde;

    #[Validate('required|numeric|min:1')]
    public $hasta;

    #[Validate('required|string')]
    public $tipo;

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
        $medidaIds = TarifaExpresoBolivariano::where('servicios_id', $servicio_id_new )
        ->pluck('medida_id');

        $medida_id = $medidaIds->first();

        $this->validate();

        $desde = strtoupper($this->desde);
        $hasta = strtoupper($this->hasta);
        $tipo   = ($this->tipo);
        $monto = strtoupper($this->monto);

        $tarifa = TarifaExpresoBolivariano::create(['desde' => $desde, 'hasta' => $hasta, 'monto' => $monto, 'activo' => true, 'tipo_expreso' => $tipo,  'medida_id' => $medida_id, 'servicios_id' => $servicio_id_new]);

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id, // Usar 'id' si 'usuario_id' es el identificador del usuario
            'accion' => 'update',
            'descripcion' => "Usuario {$usuario->id} hizo un create de una tarifa tipo Expreso Bolivariano"
        ]);
        $this->reset();
        $this->open = false;
    }
}
