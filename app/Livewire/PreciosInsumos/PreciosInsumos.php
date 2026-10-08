<?php

namespace App\Livewire\PreciosInsumos;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Insumo;
use App\Models\Parametro;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class PreciosInsumos extends Component
{
    
    public $update = false;
    public $insumo_id;
    public $nuevo_costo;
    public $buscar = '';
    public $input = 5;

    use WithPagination;

    public function editar($id)
    {
        $this->update = true;
        $this->insumo_id = $id;
        $this->nuevo_costo = Insumo::where('insumo_id', $id)->value('costo');
    }

    public function actualizar()
    {
        $this->nuevo_costo = str_replace(',', '.', $this->nuevo_costo);
        $insumo = Insumo::find($this->insumo_id);

            $insumo->costo = $this->nuevo_costo;
            $insumo->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se actualizó el precio del insumo ({$insumo->insumo_id}) a {$this->nuevo_costo}",
        ]);

        $this->update = false;
        $this->insumo_id = '';
        $this->dispatch('alertSuccess', message: 'Precio del insumo actualizado exitosamente!');
    }

    public function cerrar_editar()
    {
        $this->update = false;
    }

    public function render()
    {
        $insumos = Insumo::orderBy('insumo_id', 'asc')
            ->where('descripcion', 'like', '%'.$this->buscar.'%')
            ->paginate($this->input);
        return view('livewire.precios-insumos.precios-insumos' , compact('insumos'));
    }
}
