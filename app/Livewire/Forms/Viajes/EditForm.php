<?php

namespace App\Livewire\Forms\Viajes;

use Carbon\Carbon;
use Livewire\Form;
use App\Models\Viaje;
use App\Models\ProveedorRuta;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm extends Form
{
    public $open = false;
    public $viaje_id = '';
    public $ruta;
    public $fecha;
    public $dia_semana;
    public $proveedor_id;
    public $errorMessage = '';

    public function edit(Viaje $viaje)
    {
        $this->open = true;
        $this->resetValidation(); // Resetear validación al abrir
        $this->viaje_id = $viaje->viaje_id;
        $this->ruta = $viaje->ruta_id;
        $this->fecha = $viaje->fecha_salida;
        $this->dia_semana = $viaje->dia_semana_id;
        $this->proveedor_id = $viaje->proveedor_id;
    }

    public function update()
    {
        // Verificar que el proveedor tenga asignada la ruta solo si el proveedor no es nulo
    if (!is_null($this->proveedor_id)) {
        $existeAsignacion = ProveedorRuta::where('proveedor_id', $this->proveedor_id)
                            ->where('ruta_id', $this->ruta)
                            ->exists();

        if (!$existeAsignacion) {
            // Lanza un mensaje de error si la relación no existe
            $this->addError('proveedor_id', __('El proveedor seleccionado no tiene asignada esta ruta.'));
            $this->open = true; // Mantener el formulario abierto
            return;
        }
    }

        // Si pasa la validación, continúa con la actualización
        $viaje = Viaje::find($this->viaje_id);
        $viaje->ruta_id = $this->ruta;
        $viaje->fecha_salida =  Carbon::parse($this->fecha)->startOfWeek();
        $viaje->dia_semana_id = $this->dia_semana;

        $viaje->save();

        $user = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $user->id,
            'accion' => 'update',
            'descripcion' => "Usuario $user->id hizo un update a un viaje"
        ]);

        $this->open = false;
        $this->resetErrorBag(); // Limpiar mensajes de error al completar
    }
}
