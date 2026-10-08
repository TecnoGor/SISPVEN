<?php

namespace App\Livewire\AñadirVehiculo;
use App\Models\Estado;

use App\Models\Oficina;
use Livewire\Component;
use App\Models\Vehiculo;
use App\Models\OficinaVehiculo;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AñadirVehiculo extends Component
{
    public $estado, $oficina, $vehiculo;

    public $oficina_actual = [];
    public $estados = [];
    public $oficinas = [];
    public $vehiculos = [];

    public function mount($id)
    {
        $this->oficina_actual = Oficina::where('oficina_id', $id)->first();
        $this->estados = Estado::all();
    }

    public function rules()
    {
        return [
            'vehiculo' => 'required',
        ];
    }

    public function updatedEstado()
    {
        if(!$this->estado){
            $this->oficinas = [];
        }else{
            $this->oficinas = Oficina::where('estado_id', $this->estado)->whereNotIn('tipo_oficina_id', [5,6,7])
            ->where('externa', false)->get();
        }
    }

    public function updatedOficina()
    {
        if(!$this->oficina){
            $this->vehiculos = [];
        }else{
            $this->vehiculos = Vehiculo::where('oficina_id', $this->oficina)->get();
        }
    }


    public function guardar()
    {
        try {
            $this->validate();

            $existe = OficinaVehiculo::where('oficina_id', $this->oficina_actual['oficina_id'])
                ->where('vehiculo_id', $this->vehiculo)
                ->exists();

            if ($existe) {
                $this->dispatch('alertError', message: 'Este vehículo ya se encuentra asignado a la oficina');
                return;
            }

            OficinaVehiculo::create([
                'oficina_id' => $this->oficina_actual['oficina_id'],
                'vehiculo_id' => $this->vehiculo,
                'activo' => true,
            ]);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'create',
                'descripcion' => "Se asignó el vehículo ({$this->vehiculo}) a la oficina ({$this->oficina_actual['oficina_id']})",
            ]);

            $this->dispatch('alertSuccess', message: 'Vehículo asignado correctamente!');
        } catch (\Exception $e) {
            $this->dispatch('alertError', message: 'Ocurrió un error, revise los campos e intente de nuevo!');
        }
    }


    public function render()
    {
        return view('livewire.añadir-vehiculo.añadir-vehiculo');
    }
}
