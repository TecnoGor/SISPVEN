<?php

namespace App\Livewire\Envios;

use App\Models\Envio;
use App\Models\Oficina;
use App\Models\User;
use App\Models\Continente;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Servicio;
use Livewire\Attributes\Layout;
use Livewire\Component;


#[Layout('layouts.app')]
class DetallesEnvios extends Component
{

    public $envio, $usuario, $id, $servicio_id, $servicio, $pagar;

    public $estador, $municipior, $parroquiar;
    public $continente, $pais, $estadod, $municipiod, $parroquiad;

    public function mount($envio, $servicio_id) // Asegúrate de que este parámetro sea el correcto
    {

        $this->servicio_id = $servicio_id;
        $id = $this->envio;
        $this->envio = Envio::with('oficinas')
        ->where('envio_id', $id)
        ->first();
        $this->usuario = User::where('id', $this->envio->usuario_id)->first();

        $this->servicio = Servicio::where('servicio_id', $this->envio->servicio_id)->first();
        $this->continente = Continente::where('continente_id', $this->envio->continente_dest)->first();
        $this->pais = Pais::where('pais_id', $this->envio->pais_dest)->first();
        $this->estadod = Estado::where('estado_id', $this->envio->estado_dest)->first();
        $this->municipiod = Municipio::where('municipio_id', $this->envio->municipio_dest)->first();
        $this->parroquiad = Parroquia::where('parroquia_id', $this->envio->parroquia_dest)->first();
        $this->estador = Estado::where('estado_id', $this->envio->estado_rem)->first();
        $this->municipior = Municipio::where('municipio_id', $this->envio->municipio_rem)->first();
        $this->parroquiar = Parroquia::where('parroquia_id', $this->envio->parroquia_rem)->first();

    }

    public function render()
    {

        return view('livewire.envios.detalles-envios', ['envio' => $this->envio]);

    }

}
