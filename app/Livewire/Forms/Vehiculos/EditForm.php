<?php

namespace App\Livewire\Forms\Vehiculos;

use Livewire\Form;
use App\Models\Vehiculo;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\Storage;

class EditForm extends Form
{
    public $open = false;
    public $vehiculo_id = '';
    public $placa, $color, $año, $fecha_vecimiento, $poliza, $capacidad, $marca, $modelo, $tipo;
    public $kilometraje_actual;
    public $imagen;

    public function edit(Vehiculo $vehiculo)
    {
        $this->open = true;
        $this->vehiculo_id = $vehiculo->vehiculo_id;
        $this->tipo = $vehiculo->tipo_vehiculo_id;
        $this->placa = $vehiculo->placa;
        $this->color = $vehiculo->color;
        $this->año = $vehiculo->año;
        $this->fecha_vecimiento = $vehiculo->fecha_vencimiento;
        $this->poliza = $vehiculo->num_poliza;
        $this->capacidad = $vehiculo->capacidad_carga;
        $this->marca = $vehiculo->marca;
        $this->modelo = $vehiculo->modelo;
        $this->kilometraje_actual = $vehiculo->kilometraje_actual;
        $this->imagen = null; // No se precarga la imagen, solo se usa para nueva subida
    }

    public function update()
    {
        $this->validate([
            'kilometraje_actual' => 'required|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
        ], [
            'kilometraje_actual.required' => 'El kilometraje actual es obligatorio.',
            'kilometraje_actual.integer' => 'El kilometraje debe ser un número entero.',
            'kilometraje_actual.min' => 'El kilometraje no puede ser negativo.',
        ]);
        $vehiculo = Vehiculo::find($this->vehiculo_id );
        $vehiculo->tipo_vehiculo_id = $this->tipo;
        $vehiculo->placa = $this->placa;
        $vehiculo->color = $this->color;
        $vehiculo->año = $this->año;
        $vehiculo->fecha_vencimiento = $this->fecha_vecimiento;
        $vehiculo->num_poliza = $this->poliza;
        $vehiculo->capacidad_carga = $this->capacidad;
        $vehiculo->marca = $this->marca;
        $vehiculo->modelo = $this->modelo;
        $vehiculo->kilometraje_actual = $this->kilometraje_actual;
        // Manejo de imagen
        if ($this->imagen) {
            // Borra la imagen anterior si existe
            if ($vehiculo->imagen && Storage::disk('public')->exists($vehiculo->imagen)) {
                Storage::disk('public')->delete($vehiculo->imagen);
            }
            $vehiculo->imagen = $this->imagen->store('vehiculos', 'public');
        }
        $vehiculo->save();

        $user = auth()->user();
        \App\Models\UsuarioSeguimiento::create([
            'usuario_id' => $user->id,
            'accion' => 'update',
            'descripcion' => "Usuario $user->id realizo un update a un vehiculo de proveedor"
        ]);

        $this->open = false;
    }
}