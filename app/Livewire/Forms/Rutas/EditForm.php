<?php

namespace App\Livewire\Forms\Rutas;

use Livewire\Form;
use App\Models\Estado;
use App\Models\RutasModelo;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm extends Form
{
    public $open = false;
    public $ruta_id = '';
    public $origen;
    public $destino;
    public $distancia;
    public $tiempo;
    public $nombre;

    public function edit(RutasModelo $ruta)
    {

        $this->open = true;
        $this->ruta_id = $ruta->ruta_id;
        $this->nombre = $ruta->ruta;
        $this->origen = $ruta->plataforma_origen;
        $this->destino = $ruta->plataforma_destino;
        $this->distancia = $ruta->distancia;
        $this->tiempo = $ruta->tiempo;
    }

    public function update()
    {

        $ruta = RutasModelo::find($this->ruta_id);
        
        $ruta->distancia = $this->distancia;
        $ruta->ruta = $this->nombre; 
        $ruta->oficina_id_origen = $this->origen;
        $ruta->oficina_id_destino = $this->destino;
        $ruta->tiempo = $this->tiempo;

        $ruta->save();

        $user = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $user->id,
            'accion' => 'update',
            'descripcion' => "Usuario $user->id hizo un update a una Ruta"
        ]);

        $this->open = false;
    }
}
