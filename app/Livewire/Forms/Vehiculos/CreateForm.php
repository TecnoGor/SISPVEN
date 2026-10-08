<?php

namespace App\Livewire\Forms\Vehiculos;

use Livewire\Form;
use App\Models\Vehiculo;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class CreateForm extends Form
{
    public $open = false;
    public $placa;
    public $color;
    public $año;
    public $poliza;
    public $fecha_vecimiento;
    public $capacidad;
    public $marca;
    public $modelo;

    public function create()
    {
        $this->open = true;
    }

    public function store($proveedor_id)
    {
        $this->validate([
            'placa' => 'required',
            'color' => 'required',
            'año' => 'required|numeric',
            'fecha_vecimiento' => [
                'required',
                'date',
                'after_or_equal:' . now()->format('Y-m-d'), // Valida que la fecha no sea anterior a hoy
            ],


            'capacidad' => 'required',
            'marca' => 'required',
            'modelo' => 'required',
            'poliza' => 'required',


        ]);
        // Crear el vehículo con los datos ingresados
        $vehiculo = Vehiculo::create([
            'proveedor_id' => $proveedor_id,
            'placa' => $this->placa,
            'color' => $this->color,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'año' => $this->año,
            'num_poliza' => $this->poliza,
            'fecha_vencimiento' => $this->fecha_vecimiento,
            'capacidad_carga' => $this->capacidad,
            'Activo' => true,
        ]);

        $usuario = auth()->user();

        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'create',
            'descripcion' => "Usuario {$usuario->id} creó un vehículo.",
        ]);

        $this->reset();
        $this->open = false;
    }
}
