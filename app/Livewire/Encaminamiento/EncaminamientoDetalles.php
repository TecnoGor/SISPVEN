<?php

namespace App\Livewire\Encaminamiento;

use Livewire\Component;

use App\Models\Envio;
use App\Models\Oficina;
use App\Models\User;
use Livewire\Attributes\Layout;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioEstatus;

use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class EncaminamientoDetalles extends Component
{
    
    public $id;
    public $envio;
    public $usuario;
    public $results;
    public $status;

    public function mount($id) // Asegúrate de que este parámetro sea el correcto
    {
        // Sin esta asignacion $this->id queda null y la consulta no encuentra
        // nada: el parametro $id sombreaba a la propiedad. En la ruta funcionaba
        // solo porque Livewire hidrata las propiedades publicas desde los
        // parametros de ruta antes de llamar a mount().
        $this->id = $id;

        $this->envio = Envio::with('oficinas')->where('envio_id', $this->id)->firstOrFail();
        $this->usuario = User::where('id', $this->envio->usuario_id)->first();

        /* 
            $this->resultados = DB::table('envios_encaminamiento as es')
            ->join('envios_estatus as e', 'es.estatus_id', '=', 'e.envios_estatus_id')
            ->join('users as u', 'es.usuario_id', '=', 'u.id')
            ->join('oficinas as o', 'u.oficina_id', '=', 'o.oficina_id')
            ->select('es.*', 'e.envios_estatus_id', 'e.estatus', 'o.nombre as nombre_oficina')
            ->where('envio_id', $this->envio->envio_id)
            ->orderBy('envios_encaminamiento_id')
            ->get();
        */

            // La vista recorre cada encaminamiento leyendo envio_estatus,
            // oficina_externa y oficinas. Sin eager loading eso son 3 consultas
            // por fila del historial (N+1).
            $this->results = EnvioEncaminamiento::with(['envio_estatus', 'oficina_externa', 'oficinas'])
            ->where('envio_id', $this->envio->envio_id)
            ->orderBy('envios_encaminamiento_id')
            ->get();


            // foreach ($this->results as $resulta) { 

            //     $resulta->estatus_id = EnvioEstatus::find($resulta->estatus_id);
            //     $resulta->oficina_id = Oficina::find($resulta->oficina_id)->nombre;

            //     if (isset($resulta->oficina_externa_id)) {
            //         $resulta->oficina_externa_id = Oficina::find($resulta->oficina_externa_id);
            //     }
            // }

            $this->status = $this->results->last();


    }

    public function render()
    {
        
        return view('livewire.encaminamiento.encaminamiento-detalles', ['envio' => $this->envio]);
        
    }

}