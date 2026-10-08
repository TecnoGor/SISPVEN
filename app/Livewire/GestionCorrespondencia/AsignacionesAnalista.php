<?php

namespace App\Livewire\GestionCorrespondencia;

use App\Models\AsignacionComunicado;
use Livewire\Attributes\On;
use Livewire\Component;

class AsignacionesAnalista extends Component
{
    public string $filtro_estatus = 'Pendiente';

    public function atender($asignacion_id)
    {
        $asignacion = AsignacionComunicado::where('id', $asignacion_id)
            ->where('analista_id', auth()->id())
            ->where('estatus', 'Pendiente')
            ->first();

        if (!$asignacion) {
            session()->flash('asignacion_analista_error', 'Esa instrucción no está disponible.');
            return;
        }

        return redirect()->route('correspondencia.analista', ['asignacion' => $asignacion->id]);
    }

    #[On('asignacion-completada')]
    public function refrescar(): void
    {
        // Forzar re-render cuando el Analista acaba de guardar un comunicado ligado.
    }

    public function render()
    {
        $query = AsignacionComunicado::with(['emisor:id,name', 'comunicadoGenerado:comunicado_id,codigo'])
            ->where('analista_id', auth()->id());

        if ($this->filtro_estatus !== 'Todas') {
            $query->where('estatus', $this->filtro_estatus);
        }

        $asignaciones = $query->latest()->get();

        return view('livewire.gestion-correspondencia.asignaciones-analista', [
            'asignaciones' => $asignaciones,
        ]);
    }
}
