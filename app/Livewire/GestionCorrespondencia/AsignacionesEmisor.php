<?php

namespace App\Livewire\GestionCorrespondencia;

use App\Models\AsignacionComunicado;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AsignacionesEmisor extends Component
{
    public bool $modal_abierto = false;

    public $analista_id = '';
    public $asunto_instruccion = '';
    public $detalle_instruccion = '';
    public $tipo_documento_esperado = '';
    public $fecha_limite = '';

    public string $filtro_estatus = 'Todas';

    protected function rules(): array
    {
        return [
            'analista_id' => 'required|exists:users,id',
            'asunto_instruccion' => 'required|string|max:255',
            'detalle_instruccion' => 'required|string',
            'tipo_documento_esperado' => 'nullable|string|max:100',
            'fecha_limite' => 'nullable|date|after_or_equal:today',
        ];
    }

    protected array $messages = [
        'analista_id.required' => 'Debe seleccionar un Analista.',
        'analista_id.exists' => 'El Analista seleccionado no es válido.',
        'asunto_instruccion.required' => 'Indique un asunto breve.',
        'detalle_instruccion.required' => 'Describa la instrucción a realizar.',
        'fecha_limite.after_or_equal' => 'La fecha límite no puede estar en el pasado.',
    ];

    public function abrir_modal(): void
    {
        $this->reset(['analista_id', 'asunto_instruccion', 'detalle_instruccion', 'tipo_documento_esperado', 'fecha_limite']);
        $this->resetErrorBag();
        $this->modal_abierto = true;
    }

    public function cerrar_modal(): void
    {
        $this->modal_abierto = false;
    }

    public function emitir_instruccion(): void
    {
        $datos = $this->validate();

        AsignacionComunicado::create([
            'codigo' => AsignacionComunicado::generarCodigo(),
            'emisor_id' => auth()->id(),
            'analista_id' => $datos['analista_id'],
            'asunto_instruccion' => $datos['asunto_instruccion'],
            'detalle_instruccion' => $datos['detalle_instruccion'],
            'tipo_documento_esperado' => $datos['tipo_documento_esperado'] ?: null,
            'fecha_limite' => !empty($datos['fecha_limite']) ? Carbon::parse($datos['fecha_limite']) : null,
            'estatus' => 'Pendiente',
        ]);

        $this->cerrar_modal();
        session()->flash('asignacion_emisor_ok', 'Instrucción emitida correctamente.');
    }

    public function render()
    {
        $analistas = User::role('Analista Correspondencia')
            ->orderBy('name')
            ->get(['id', 'name']);

        $query = AsignacionComunicado::with(['analista:id,name', 'comunicadoGenerado:comunicado_id,codigo'])
            ->where('emisor_id', auth()->id());

        if ($this->filtro_estatus !== 'Todas') {
            $query->where('estatus', $this->filtro_estatus);
        }

        $asignaciones = $query->latest()->get();

        return view('livewire.gestion-correspondencia.asignaciones-emisor', [
            'analistas' => $analistas,
            'asignaciones' => $asignaciones,
        ]);
    }
}
