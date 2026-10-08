<?php

namespace App\Livewire\Encaminamiento;

use App\Models\Envio;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')] 
class Encaminamiento extends Component
{
    public $codigo, $envio, $buscar, $search;
    public $isNumericInput = false;

    public function updatedBuscar($value)
    {
        $this->isNumericInput = in_array($this->buscar, [2, 3]);
    }

    public function getPathingByCI()
    {

        $this->validate([
            'search' => 'required|numeric',
        ], [
            'search.required' => 'Este campo es obligatorio.',
        ]);

            if ($this->buscar == 2) {
                $this->envio = Envio::where('documento_rem', $this->search)
                ->whereNotNull('nombre_dest')
                ->get();
                if ($this->envio->isEmpty()) {
                    toastr()->error('No existe ningún seguimiento con este Documento de Identidad');
                }
            }

            elseif ($this->buscar == 3) {
                $this->envio = Envio::where('documento_dest', $this->search)->get();
                if ($this->envio->isEmpty()) {
                    toastr()->error('No existe ningún seguimiento con este Documento de Identidad');
                }
            }

            
        
    }

    public function getPathing()
    {
        
        if ($this->buscar == 1) {

            $this->validate([

                'search' => 'required',
    
            ], [
    
                'search.required' => 'Este campo es obligatorio.',
    
            ]);

            $this->envio = Envio::where('codigo_envio', $this->search)
            ->whereNotNull('nombre_dest')
            ->first();

            if (!$this->envio) {

                toastr()->error('No existe ningún seguimiento con este código de envío.');

            }
            else {
                return redirect()->route('encaminamiento.encaminamiento-detalles', ['id' => $this->envio->envio_id]);
            } 
        }
        else if ($this->buscar == 2 || $this->buscar == 3) {
            $this->getPathingByCI();
        }
        else {
           
                toastr()->error('Debe seleccionar un método de búsqueda.');
    
        }

    }

    public function mount()
    {
        
        $this->dispatch('formatNumber');

    }


    public function render()
    {

        return view('livewire.encaminamiento.encaminamiento');

    }
}
