<?php

namespace App\Livewire\CreacionJudicialTribunal;

use Livewire\Component;
use Livewire\WithPagination; // Importamos paginación por si la lista crece mucho
use App\Models\LugarEmisionTelegrama;
use App\Models\CircuitoJudicialTribunalTelegrama;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreacionJudicialTribunal extends Component
{
    use WithPagination;

    // 1. VARIABLES DEL FORMULARIO
    public $lugar_emision_id = '';
    public $nombre = '';

    // 2. VARIABLES DE INTERFAZ 
    public $showModal = false; // Controla si se abre o cierra el modal
    public $search = '';       // Controla el buscador

    // Propiedades estáticas (Selects)
    public $tiposEmision;

    // Reglas de validación
    protected $rules = [
        'lugar_emision_id' => 'required|exists:lugar_emision_telegramas,lugar_emision_telegramas_id', 
        'nombre' => 'required|string|max:255|unique:circuito_judicial_tribunal_telegramas,nombre',
    ];

    // Atributos para mostrar errores 
    protected $validationAttributes = [
        'lugar_emision_id' => 'tipo de registro', 
        'nombre' => 'nombre del circuito/tribunal',
    ];

    public function mount()
    {
        $this->tiposEmision = LugarEmisionTelegrama::all();
    }

    // Resetea la paginación al escribir en el buscador
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function guardar()
    {
        $this->validate();

        $nuevo_circuito = CircuitoJudicialTribunalTelegrama::create([
            'lugar_emision_telegramas_id' => $this->lugar_emision_id,
            'nombre' => $this->nombre,
            'activo' => true,
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se creó el circuito/tribunal judicial ({$nuevo_circuito->getKey()}) con nombre '{$this->nombre}'",
        ]);

        // Limpia los campos
        $this->reset(['lugar_emision_id', 'nombre']);

        // Mensaje de éxito
        session()->flash('message', '¡Registro guardado exitosamente!');
        $this->showModal = false;
    }

    public function toggleActivo($id)
    {
        $registro = CircuitoJudicialTribunalTelegrama::findOrFail($id);
        $registro->activo = !$registro->activo;
        $registro->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se cambió el estatus del circuito/tribunal judicial ({$id}) a " . ($registro->activo ? 'activo' : 'inactivo'),
        ]);

        session()->flash('message', '¡Estado actualizado correctamente!');
    }

    public function render()
    {
        $query = CircuitoJudicialTribunalTelegrama::leftJoin(
            'lugar_emision_telegramas',
            'circuito_judicial_tribunal_telegramas.lugar_emision_telegramas_id',
            '=',
            'lugar_emision_telegramas.lugar_emision_telegramas_id'
        )
        ->select(
            'circuito_judicial_tribunal_telegramas.*', 
            'lugar_emision_telegramas.nombre as nombre_lugar'
        );

        // 4. LÓGICA DEL BUSCADOR
        if (!empty($this->search)) {
            $query->where(function($q) {
                // Busca por nombre del circuito O por nombre del tipo
                $q->where('circuito_judicial_tribunal_telegramas.nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('lugar_emision_telegramas.nombre', 'like', '%' . $this->search . '%');
            });
        }

        // Ordenamos y obtenemos los resultados
        $listado = $query->latest('circuito_judicial_tribunal_telegramas.created_at')->get();

        return view('livewire.creacion-judicial-tribunal.creacion-judicial-tribunal', [
            'listado' => $listado
        ]);
    }
}