<?php

namespace App\Livewire\Encaminamiento;

use Livewire\Component;

use App\Models\Envio;
use App\Models\Oficina;
use App\Models\User;
use Livewire\Attributes\Layout;
use App\Models\EnvioEncaminamiento;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class EncaminamientoExternoDetalles extends Component
{
    
    public $id;
    public $envio;
    public $usuario;
    public $resultados;
    public $status;

    public function mount($id) // Asegúrate de que este parámetro sea el correcto
    {
        
        $this->envio = Envio::with('oficinas')->where('envio_id', $this->id)->first();
        $this->usuario = User::where('id', $this->envio->usuario_id)->first();

        $this->resultados = DB::table('envios_encaminamiento as es')
            ->join('envios_estatus as e', 'es.estatus_id', '=', 'e.envios_estatus_id')
            ->join('users as u', 'es.usuario_id', '=', 'u.id')
            ->join('oficinas as o', 'u.oficina_id', '=', 'o.oficina_id')
            ->select('es.*', 'e.envios_estatus_id', 'e.estatus', 'o.nombre as nombre_oficina')
            ->where('envio_id', $this->envio->envio_id)
            ->orderBy('envios_encaminamiento_id')
            ->get();

            $this->status = $this->resultados->last();


    }

    public function render()
    {
        
        return view('livewire.encaminamiento.encaminamientoext-detalles', ['envio' => $this->envio]);
        
    }

}