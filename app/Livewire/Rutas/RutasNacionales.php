<?php

namespace App\Livewire\Rutas;

use App\Models\Estado;
use App\Models\Oficina;
use App\Models\RutasModelo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\RutaPuntoEntrega;
use App\Models\UsuarioSeguimiento;
use App\Livewire\Forms\Rutas\EditForm;
use App\Livewire\Forms\Rutas\CreateForm;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Exports\RutasExport;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class RutasNacionales extends Component
{
    use WithPagination;

    public $search;
    public $filter = 'all';
    public $perPage = 5;
    public $sortBy = 'ruta_id';
    public $sortDir = 'DESC';
    public CreateForm $createForm;
    public EditForm $editForm;
    public $puntosEntrega = [];
    public $expandedRutas = [];

    public $oficinas;

    public function mount()
    {
        $this->oficinas = Oficina::whereIn('tipo_oficina_id', [1, 2, 3, 4, 7])->where('externa', false)->get();
    }

    public function create()
    {
        $this->resetValidation();
        $this->createForm->create();
    }

    public function store()
    {
        if ($this->createForm->store()) {
            $this->dispatch('alertSuccess', message: 'Ruta creada exitosamente!');
        } else {
            $this->dispatch('alertError', message: 'Ocurrió un error al crear la ruta. Intente de nuevo.');
        }
    }

    public function edit(RutasModelo $ruta)
    {
        $this->resetValidation();
        $this->editForm->edit($ruta);
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Ruta editada exitosamente!');
    }

    public function togglePuntosEntrega($rutaId)
    {
        if (in_array($rutaId, $this->expandedRutas)) {
            $this->expandedRutas = array_diff($this->expandedRutas, [$rutaId]);
        } else {
            $this->expandedRutas[] = $rutaId;
        }
    }

    public function desactivar(RutasModelo $ruta)
    {
        $ruta->activo = false;
        $ruta->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó la ruta nacional ({$ruta->ruta_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Ruta Desactivada exitosamente!');
    }

    public function activar(RutasModelo $ruta)
    {
        $ruta->activo = true;
        $ruta->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó la ruta nacional ({$ruta->ruta_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Ruta Activada exitosamente!');
    }

    public function addPuntoEntrega()
    {
        $this->puntosEntrega[] = null;
    }

    public function removePuntoEntrega($index)
    {
        if (isset($this->createForm->puntosEntrega[$index])) {
            unset($this->createForm->puntosEntrega[$index]);
            $this->createForm->puntosEntrega = array_values($this->createForm->puntosEntrega);
        }

        $this->resetErrorBag('createForm.puntosEntrega');
    }

    public function exportarPDF()
    {
        $rutas = RutasModelo::when($this->search, function ($query) {
                $query->where('ruta', 'LIKE', '%' . $this->search . '%');
            })
            ->whereHas('oficinaOrigen', function ($query) {
                $query->whereIn('tipo_oficina_id', [1, 2, 3, 4, 7]);
            })
            ->whereHas('oficinaDestino', function ($query) {
                $query->whereIn('tipo_oficina_id', [1, 2, 3, 4, 7]);
            })
            ->with(['puntosEntrega.oficina'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->get();

        $usuario = auth()->user();

        $oficinaId = DB::table('users')
            ->where('id', $usuario->id)
            ->value('oficina_id');

        $nombreOficinaUsuario = $oficinaId
            ? Oficina::where('oficina_id', $oficinaId)->value('nombre')
            : null;

        $pdf = Pdf::loadView('pdf.reporte-rutas-nacional', [
            'rutas' => $rutas,
            'oficinas' => $this->oficinas,
            'nombreOficinaUsuario' => $nombreOficinaUsuario,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_rutas_nacionales.pdf');
    }

    public function exportarExcel()
    {
        $rutas = RutasModelo::when($this->search, function ($query) {
                $query->where('ruta', 'LIKE', '%' . $this->search . '%');
            })
            ->whereHas('oficinaOrigen', function ($query) {
                $query->whereIn('tipo_oficina_id', [1, 2, 3, 4, 7]);
            })
            ->whereHas('oficinaDestino', function ($query) {
                $query->whereIn('tipo_oficina_id', [1, 2, 3, 4, 7]);
            })
            ->with(['puntosEntrega.oficina'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->get();

        $usuario = auth()->user();

        $oficinaId = DB::table('users')
            ->where('id', $usuario->id)
            ->value('oficina_id');

        $nombreOficinaUsuario = $oficinaId
            ? Oficina::where('oficina_id', $oficinaId)->value('nombre')
            : null;

        return Excel::download(new RutasExport($rutas, $nombreOficinaUsuario), 'reporte_rutas_nacionales.xlsx');
    }

    public function render()
    {
        $rutas = RutasModelo::when($this->search, function ($query) {
                $query->where('ruta', 'LIKE', '%' . $this->search . '%');
            })
            ->whereHas('oficinaOrigen', function ($query) {
                $query->whereIn('tipo_oficina_id', [1, 2, 3, 4, 7]);
            })
            ->whereHas('oficinaDestino', function ($query) {
                $query->whereIn('tipo_oficina_id', [1, 2, 3, 4, 7]);
            })
            ->with(['puntosEntrega.oficina'])
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.rutas.rutas-nacionales', [
            'rutas' => $rutas,
        ]);
    }
}
