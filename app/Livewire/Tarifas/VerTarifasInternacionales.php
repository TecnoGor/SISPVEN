<?php

namespace App\Livewire\Tarifas;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\TarifaInternacionalRango;
use App\Models\TarifaInternacionalConcepto;
use App\Models\UsuarioSeguimiento;
use App\Livewire\Forms\TarifasRagosInternacional\EditForm;
use App\Livewire\Forms\TarifasRagosInternacional\CreateForm;
use App\Livewire\Forms\TarifasRagosInternacional\CreateForm2;
use App\Livewire\Forms\TarifasRagosInternacional\EditForm2;

#[Layout('layouts.app')] 
class VerTarifasInternacionales extends Component
{
    use WithPagination;
    public $search = '';
    public $perPage = 5;
    public $sortBy = 'servicio_id';
    public $sortDir = 'ASC';
    public $servicio_id;
    public $informacion = [];
    public $origenTabla; 
    public $noInformacion = false;

    public CreateForm $createForm;
    public CreateForm2 $createForm2;
    public EditForm $editForm;
    public EditForm2 $editForm2;

    public function mount($servicio_id)
    {
        $this->servicio_id = $servicio_id;  
        $this->fetchInformacion();
    }

    public function edit(TarifaInternacionalRango $id)
    {
        $this->resetValidation();
        $this->editForm->edit($id);
    }

    public function editconcep(TarifaInternacionalConcepto $id2)
    {
        $this->resetValidation();
        $this->editForm2->editconcep($id2);
    }

    public function create()
    {
        $this->resetValidation();
        $this->createForm->create($this->servicio_id);
    }

    public function createFormconcep()
    {
        $this->resetValidation();
        $this->createForm2->create($this->servicio_id);
    }

    public function store()
    {
        $this->createForm->store();
        $this->dispatch('alertSuccess', message: 'Tarifa creado exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion(); 
    }

    public function storeconcep()
    {
        $this->createForm2->store();
        $this->dispatch('alertSuccess', message: 'Tarifa creado exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion(); 
    }

    public function fetchInformacion()
    {
        $this->noInformacion = false;
        $this->origenTabla = null;

        // Si no se encuentra, intenta en la segunda tabla
        $this->informacion = TarifaInternacionalRango::where('servicios_id', $this->servicio_id)->get()->toArray();
        if (!empty($this->informacion)) {
            $this->origenTabla = 'rango'; // Indicar que proviene de la tabla TarifaNacionalRango
            return;
        }

        // Si aún no se encuentra, intenta en la tercera tabla
        $this->informacion = TarifaInternacionalConcepto::where('servicios_id', $this->servicio_id)->get()->toArray();
        if (!empty($this->informacion)) {
            $this->origenTabla = 'concepto'; // Indicar que proviene de la tabla TarifaNacionalConcepto
            return;
        }

        $this->noInformacion = true;
        $this->dispatch('alertError', message: 'No se encontró tarifa para el servicio seleccionado.');
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Tarifa editado exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function updateconcep()
    {
        $this->editForm2->updateconcep();
        $this->dispatch('alertSuccess', message: 'Tarifa editado exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function desactivar(TarifaInternacionalRango $tarifa)
    {
        $tarifa->activo = false;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó la tarifa internacional por rango ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Desactivada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function activar(TarifaInternacionalRango $tarifa)
    {
        $tarifa->activo = true;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó la tarifa internacional por rango ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Activada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function desactivarconcep(TarifaInternacionalConcepto $tarifa)
    {
        $tarifa->activo = false;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó la tarifa internacional por concepto ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Desactivada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function activarconcep(TarifaInternacionalConcepto $tarifa)
    {
        $tarifa->activo = true;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó la tarifa internacional por concepto ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Activada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function render()
    {
        if ($this->origenTabla === 'concepto') {
            return view('livewire.tarifas.ver-tarifas-conceptos-internacional', [
                'informacion' => $this->informacion,
            ]);
        } elseif ($this->origenTabla === 'rango') {  
            return view('livewire.tarifas.ver-tarifas-internacionales', [
                'informacion' => $this->informacion,
                'noInformacion' => $this->noInformacion
            ]);
        }

        return view('livewire.tarifas.ver-tarifas-internacionales', [
            'informacion' => $this->informacion,
            'noInformacion' => $this->noInformacion
        ]);
    }
}
