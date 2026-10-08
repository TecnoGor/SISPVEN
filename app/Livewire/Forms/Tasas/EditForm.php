<?php

namespace App\Livewire\Forms\Tasas;

use App\Models\Parametro;
use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm extends Form
{
    public $parametro_id= '';
    public $open = false;

    public $nombre;

    public $valor;

    public function edit(Parametro $tasa)
    {
        $this->open = true;
        $this->parametro_id = $tasa->parametro_id;
        $this->nombre = $tasa->nombre;
        // Formato bancario: 1.234,56
        $this->valor = number_format($tasa->valor, 2, ',', '.');
    }

    public function update()
    {

        $tasa = Parametro::find($this->parametro_id);
        
        // Limpiar formato bancario para guardar en base de datos
        // Ej: "1.234,56" -> "1234.56"
        $cleanValor = str_replace('.', '', $this->valor);
        $cleanValor = str_replace(',', '.', $cleanValor);
        
        // Actualizar solo los campos permitidos
        $tasa->nombre = $this->nombre;
        $tasa->valor = (float) $cleanValor;


        $tasa->save();

        $user = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $user ->id,
            'accion' => 'update',
            'descripcion' => "Usuario $user->id hizo un update a un parametro de tasas"
        ]);

        $this->open = false;
    }
}
