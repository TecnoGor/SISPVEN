<?php

namespace App\Livewire\Tarifas;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\TarifaNacionalRango;
use App\Models\TarifaNacionalConcepto;
use App\Models\TarifaExpresoBolivariano;
use App\Models\UsuarioSeguimiento;
use App\Livewire\Forms\TarifasRagos\EditForm;
use App\Livewire\Forms\TarifasRagos\EditForm2;
use App\Livewire\Forms\TarifasRagos\EditForm3;
use App\Livewire\Forms\TarifasRagos\CreateForm;
use App\Livewire\Forms\TarifasRagos\CreateForm2;
use App\Livewire\Forms\TarifasRagos\CreateForm3;

#[Layout('layouts.app')]
class VerTarifas extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;
    public $sortBy = 'id';
    public $sortDir = 'ASC';
    public $servicio_id;
    public $informacion = [];
    public $origenTabla;
    public $noInformacion = false;

    // Forms inyectados
    public CreateForm $createForm;
    public CreateForm2 $createForm2;
    public CreateForm3 $createForm3;
    public EditForm $editForm;
    public EditForm2 $editForm2;
    public EditForm3 $editForm3;

    public function mount($servicio_id)
    {
        $this->servicio_id = $servicio_id;
        $this->fetchInformacion();
    }

    public function updatedServicioId()
    {
        $this->fetchInformacion();
    }

    // --- CRUD / acciones ---
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

    public function createexpreso()
    {
        $this->resetValidation();
        $this->createForm3->create($this->servicio_id);
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

    public function storeexpreso()
    {
        $this->createForm3->store();
        $this->dispatch('alertSuccess', message: 'Tarifa creado exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function edit(TarifaNacionalRango $id)
    {
        $this->resetValidation();
        $this->editForm->edit($id);
    }

    public function editconcep(TarifaNacionalConcepto $id2)
    {
        $this->resetValidation();
        $this->editForm2->editconcep($id2);
    }

    public function editexpreso(TarifaExpresoBolivariano $id3)
    {
        $this->resetValidation();
        $this->editForm3->edit($id3);
    }

    public function desactivar(TarifaNacionalRango $tarifa)
    {
        $tarifa->activo = false;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó la tarifa nacional por rango ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Desactivada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function activar(TarifaNacionalRango $tarifa)
    {
        $tarifa->activo = true;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó la tarifa nacional por rango ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Activada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function desactivarconcep(TarifaNacionalConcepto $tarifa)
    {
        $tarifa->activo = false;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó la tarifa nacional por concepto ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Desactivada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function activarconcep(TarifaNacionalConcepto $tarifa)
    {
        $tarifa->activo = true;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó la tarifa nacional por concepto ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Activada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function desactivarexpreso(TarifaExpresoBolivariano $tarifa)
    {
        $tarifa->activo = false;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó la tarifa Expreso Bolivariano ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Desactivada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function activarexpreso(TarifaExpresoBolivariano $tarifa)
    {
        $tarifa->activo = true;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó la tarifa Expreso Bolivariano ({$tarifa->getKey()})",
        ]);

        $this->dispatch('alertSuccess', message: 'Tarifa Activada exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion();
    }

    public function fetchInformacion()
    {
        $this->noInformacion = false;
        
        // Intenta buscar en la primera tabla
        $this->informacion = TarifaExpresoBolivariano::where('servicios_id', $this->servicio_id)->get()->toArray();
        if (!empty($this->informacion)) {
            $this->origenTabla = 'expreso'; // Indicar que proviene de la tabla TarifaExpresoBolivariano
            return;
        }

        // Si no se encuentra, intenta en la segunda tabla
        $this->informacion = TarifaNacionalRango::where('servicios_id', $this->servicio_id)->get()->toArray();
        if (!empty($this->informacion)) {
            $this->origenTabla = 'rango'; // Indicar que proviene de la tabla TarifaNacionalRango
            return;
        }

        // Si aún no se encuentra, intenta en la tercera tabla
        $this->informacion = TarifaNacionalConcepto::where('servicios_id', $this->servicio_id)->get()->toArray();
        if (!empty($this->informacion)) {
            $this->origenTabla = 'concepto'; // Indicar que proviene de la tabla TarifaNacionalConcepto
            return;
        }

        $this->noInformacion = true;
        $this->dispatch('tarifa-not-found', message: 'No se encontró una tarifa asociada al servicio seleccionado.');
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

    public function updateexpreso()
    {
        $this->editForm3->update();
        $this->dispatch('alertSuccess', message: 'Tarifa editado exitosamente!');
        $this->dispatch('tarifaUpdated');
        $this->fetchInformacion(); 
    }

    public function render()
    {
        $tarifas = TarifaNacionalRango::when($this->search, function($query) {
            $query->where('desde', 'LIKE', '%'. $this->search.'%');
        })->paginate($this->perPage);

        if ($this->origenTabla === 'expreso') {
            return view('livewire.tarifas.ver-tarifas-expresos', [
                'informacion' => $this->informacion,
            ]);
        } elseif ($this->origenTabla === 'concepto') {
            return view('livewire.tarifas.ver-tarifas-conceptos', [
                'informacion' => $this->informacion,
            ]);
        } elseif ($this->origenTabla === 'rango') {  
            return view('livewire.tarifas.ver-tarifas', [
                'tarifas' => $tarifas,
                'informacion' => $this->informacion,
                'noInformacion' => $this->noInformacion
            ]);
        }

        return view('livewire.tarifas.ver-tarifas', [
            'informacion' => $this->informacion,
            'noInformacion' => $this->noInformacion
        ]);
    }
}
