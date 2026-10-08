<?php

namespace App\Livewire\Tarifas;

use Livewire\Component;
use App\Models\Servicio;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\Servicios\CreateForm;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MostrarTarifasInternacionales extends Component
{
    use WithPagination;
    public $search = '';
    public $perPage = 5;
    public $sortBy = 'servicio_id';
    public $sortDir = 'ASC';
    public CreateForm $CreateForm;

    public function updatingSearch()
    {
        $this->resetPage('pageInt');
    }

    public function updatingPerPage()
    {
        $this->resetPage('pageInt');
    }


    public function create()
    {
        $this->resetValidation();
        $this->CreateForm->create();
    }

    public function store()
    {
        $this->CreateForm->store();
        $this->dispatch('alertSuccess', message: 'Servicio creado exitosamente!');
    }

    public function toggleCobroEntrega($servicio_id)
    {
        $servicio = Servicio::find($servicio_id);
        if (!$servicio) {
            $this->dispatch('alertSuccess2', message: 'Servicio no encontrado.');
            return;
        }

        $servicio->cobra_entrega = !$servicio->cobra_entrega;
        $servicio->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se " . ($servicio->cobra_entrega ? 'activó' : 'desactivó') . " el cobro en entrega del servicio internacional ({$servicio->servicio_id})",
        ]);

        $estado = $servicio->cobra_entrega ? 'activado' : 'desactivado';
        $this->dispatch('alertSuccess', message: "Cobro en entrega {$estado} correctamente.");
    }

    public function toggleCobroExcedente($servicio_id)
    {
        $servicio = Servicio::find($servicio_id);
        if (!$servicio) {
            $this->dispatch('alertSuccess2', message: 'Servicio no encontrado.');
            return;
        }

        $servicio->cobra_excedente = !$servicio->cobra_excedente;
        $servicio->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se " . ($servicio->cobra_excedente ? 'activó' : 'desactivó') . " el cobro de excedente del servicio internacional ({$servicio->servicio_id})",
        ]);

        $estado = $servicio->cobra_excedente ? 'activado' : 'desactivado';
        $this->dispatch('alertSuccess', message: "Cobro de excedente {$estado} correctamente.");
    }

    public function toggleCobroAlmacenaje($servicio_id)
    {
        $servicio = Servicio::find($servicio_id);
        if (!$servicio) {
            $this->dispatch('alertSuccess2', message: 'Servicio no encontrado.');
            return;
        }

        $servicio->cobra_almacenaje = !$servicio->cobra_almacenaje;
        $servicio->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se " . ($servicio->cobra_almacenaje ? 'activó' : 'desactivó') . " el cobro de almacenaje del servicio internacional ({$servicio->servicio_id})",
        ]);

        $estado = $servicio->cobra_almacenaje ? 'activado' : 'desactivado';
        $this->dispatch('alertSuccess', message: "Cobro de almacenaje {$estado} correctamente.");
    }

    public function toggleCobroAvisosLlegada($servicio_id)
    {
        $servicio = Servicio::find($servicio_id);
        if (!$servicio) {
            $this->dispatch('alertSuccess2', message: 'Servicio no encontrado.');
            return;
        }

        $servicio->cobra_avisos_llegada = !$servicio->cobra_avisos_llegada;
        $servicio->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se " . ($servicio->cobra_avisos_llegada ? 'activó' : 'desactivó') . " el cobro de avisos de llegada del servicio internacional ({$servicio->servicio_id})",
        ]);

        $estado = $servicio->cobra_avisos_llegada ? 'activado' : 'desactivado';
        $this->dispatch('alertSuccess', message: "Cobro de avisos de llegada {$estado} correctamente.");
    }

    public function render()
    {
        $servicios = Servicio::when($this->search, function($query) {
            $query->where('nombre', 'LIKE', '%'. $this->search.'%');
        })
        ->where('nacional', false)
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage, pageName: 'pageInt');
        return view('livewire.tarifas.mostrar-tarifas-internacionales', compact('servicios'));
    }
}
