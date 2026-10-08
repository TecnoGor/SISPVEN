<?php

namespace App\Livewire\Envios;

use App\Models\Envio;
use Livewire\Attributes\Layout;
use Livewire\Component;

use Livewire\Attributes\On;
use Livewire\WithPagination;


#[Layout('layouts.app')] 
class ConsultaEnvios extends Component
{

    use WithPagination;

    public $codigo, $envio, $buscar, $search, $servicio_id, $desde, $hasta, $oficina_id;
    public $isNumericInput = false;
    
    public function updatedBuscar($value)
    {
        $this->isNumericInput = in_array($this->buscar, [2]);
    }

    public function getEnvioById()
    {

        $this->validate([

            'search' => 'required',

        ], [

            'search.required' => 'Este campo es obligatorio.',

        ]);

        if (auth()->user()->oficina_id == NULL) {

        $this->envio = Envio::where('servicio_id', $this->servicio_id)
        ->where('codigo_envio', $this->search)->first();

        }
        else {
            
            $this->envio = Envio::where('servicio_id', $this->servicio_id)
            ->where('oficina_id', auth()->user()->oficina_id)
            ->where('codigo_envio', $this->search)->first();

        }

        if (!$this->envio) {

            toastr()->error('No existe ningún envío con este código acorde al servicio actual.');

        }
        else {
            return redirect()->route('envios.detalles-envios', ['envio' => $this->envio->envio_id, 'servicio_id' => $this->envio->servicio_id]);
        } 



    }
    public function getEnvioByCI()
    {

        $this->validate([
            'search' => 'required|numeric',
        ], [
            'search.required' => 'Este campo es obligatorio.',
            'search.numeric' => 'Este formato debe ser numérico.',
        ]);

        if (auth()->user()->oficina_id == NULL) {

            $this->envio = Envio::where(function($query) {
                $query->where('documento_rem', $this->search)
                ->orWhere('documento_dest', $this->search);
            })
            ->where('servicio_id', $this->servicio_id)
            ->get();

        }
        else {
            $this->envio = Envio::where(function($query) {
                $query->where('documento_rem', $this->search)
                ->orWhere('documento_dest', $this->search);
            })
            ->where('servicio_id', $this->servicio_id)
            ->where('oficina_id', auth()->user()->oficina_id)
            ->get();
        }

            if ($this->envio->isEmpty()) {
                toastr()->error('No existe ningún envío con este Documento de Identidad acorde al servicio actual');
            }
        
    }
    public function getEnvio()
    {
        
        if ($this->buscar == 1) {
            $this->getEnvioById();
        }
        else if ($this->buscar == 2) {
            $this->getEnvioByCI();
        }
        else {
            toastr()->error('Debe seleccionar un método de búsqueda.');
        }
        
    }

    public function mount($servicio_id, $oficina_id = null)
    {
        $this->dispatch('formatNumber');
        $this->servicio_id = $servicio_id;
        $this->oficina_id = $oficina_id;
    }


    public function render()
    {
            
        $fechaInicio = $this->desde;
        $fechaFin = $this->hasta;

        if ((!$fechaInicio)||(!$fechaFin)) {
            $fechaInicio = now()->startOfDay();
            $fechaFin = now()->endOfDay();

            $this->desde = now()->format('Y-m-d');
            $this->hasta = now()->format('Y-m-d');
        }
        else {
            $fechaInicio = $fechaInicio."  00:00:00";
            $fechaFin = $fechaFin."  23:59:59";
        }

        if (auth()->user()->oficina_id == NULL) {

        $this->envio = Envio::where('servicio_id', $this->servicio_id)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])->get();

        }
        else {
            
        $this->envio = Envio::where('servicio_id', $this->servicio_id)
        ->where('oficina_id', auth()->user()->oficina_id)
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])->get();

        }
        
        return view('livewire.envios.consulta-envios');
    }
}
