<?php

namespace App\Livewire\Rutas;

use App\Models\Oficina;
use App\Models\Estado;
use Livewire\Component;
use App\Models\RutasModelo;
use App\Models\UsuarioSeguimiento;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\Rutas\EditForm;
use App\Livewire\Forms\Rutas\CreateForm;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RutasExport;

#[Layout('layouts.app')]
class RutasLocales extends Component
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
    public $oficinasConEstado;

    public function mount()
    {
        $usuario = auth()->user();

        if ($usuario->hasRole('Gerente de Estado')) {
            // El estado del gerente se deriva de la oficina a la que pertenece
            // (más sus estados agrupados), no de la tabla usuario_estados.
            $estadosCubiertos = Estado::estadosCubiertos($usuario->oficina?->estado_id);

            $this->oficinasConEstado = Oficina::where('tipo_oficina_id', 4)->where('externa', false)
                ->whereIn('estado_id', $estadosCubiertos)
                ->get();

            $this->oficinas = Oficina::whereIn('tipo_oficina_id', [1, 2, 3])
                ->whereIn('estado_id', $estadosCubiertos)
                ->get();
        } elseif ($usuario->hasRole(['SuperAdmin', 'Operaciones Nacionales'])) {
            $this->oficinasConEstado = Oficina::where('tipo_oficina_id', 4)->where('externa', false)->get();
            $this->oficinas = Oficina::whereIn('tipo_oficina_id', [1, 2, 3])->get();
        } else {
            // Resto de roles: como origen solo se ofrece su propia oficina, en
            // lugar de dejar el select vacío.
            $this->oficinasConEstado = $usuario->oficina
                ? collect([$usuario->oficina])
                : collect();

            // Como destino, las oficinas de su estado y de los estados asociados
            // (los que comparten la misma COP).
            $estadosCubiertos = Estado::estadosCubiertos(
                Estado::estadoPrincipal($usuario->oficina?->estado_id)
            );

            $this->oficinas = Oficina::whereIn('tipo_oficina_id', [1, 2, 3])
                ->whereIn('estado_id', $estadosCubiertos)
                ->get();
        }
    }

    public function create()
    {
        $this->resetValidation();
        $this->createForm->create();
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

    public function store()
    {
        if ($this->createForm->store()) {
            $this->dispatch('alertSuccess', message: 'Ruta creada exitosamente!');
        } else {
            $this->dispatch('alertError', message: 'Ocurrió un error al crear la ruta. Intente de nuevo.');
        }
    }

    public function desactivar(RutasModelo $ruta)
    {
        $ruta->activo = false;
        $ruta->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó la ruta local ({$ruta->ruta_id})",
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
            'descripcion' => "Se activó la ruta local ({$ruta->ruta_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Ruta Activada exitosamente!');
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

    private function getRutasFiltradas($paginar = true)
    {
        $usuario = auth()->user();

        $query = RutasModelo::query();

        if ($usuario->hasRole('Gerente de Estado')) {
            // Ámbito del gerente derivado de su oficina (más estados agrupados).
            $estadosCubiertos = Estado::estadosCubiertos($usuario->oficina?->estado_id);

            $oficinasOrigenIds = Oficina::whereIn('estado_id', $estadosCubiertos)
                ->pluck('oficina_id');

            $query->whereIn('oficina_id_origen', $oficinasOrigenIds);
        }

        if ($usuario->hasRole('Gerente de Encaminamiento')) {
            $oficinaId = DB::table('users')
                ->where('id', $usuario->id)
                ->value('oficina_id');

            $query->where(function ($subquery) use ($oficinaId) {
                $subquery->where('oficina_id_origen', $oficinaId)
                    ->orWhere('oficina_id_destino', $oficinaId)
                    ->orWhereHas('puntosEntrega', function ($q) use ($oficinaId) {
                        $q->where('oficina_id', $oficinaId);
                    });
            });
        }

        if ($usuario->hasRole(['SuperAdmin', 'Operaciones Nacionales'])) {
            // Mostrar todas las rutas
        }

        if ($this->search) {
            $query->where('ruta', 'LIKE', '%' . $this->search . '%');
        }

        $query->whereHas('oficinaDestino', function ($query) {
            $query->whereIn('tipo_oficina_id', [1, 2, 3, 7]);
        });

        $query->with(['puntosEntrega.oficina'])
            ->orderBy($this->sortBy, $this->sortDir);

        return $paginar ? $query->paginate($this->perPage) : $query->get();
    }

    public function exportarExcel()
    {
        $rutas = $this->getRutasFiltradas(false); // sin paginación, con filtros aplicados
        $usuario = auth()->user();

        // Obtener la oficina_id del usuario
        $oficinaId = DB::table('users')
            ->where('id', $usuario->id)
            ->value('oficina_id');

        // Obtener el nombre de la oficina (si existe)
        $nombreOficinaUsuario = null;
        if ($oficinaId) {
            $nombreOficinaUsuario = Oficina::where('oficina_id', $oficinaId)->value('nombre');
        }

        // Generar y descargar el archivo Excel, pasando rutas y nombre de oficina al export
        return Excel::download(new RutasExport($rutas, $nombreOficinaUsuario), 'reporte_rutas.xlsx');
    }



    public function exportarPDF()
    {
        $rutas = $this->getRutasFiltradas(false); // sin paginación
        $usuario = auth()->user();

        // Obtener oficina_id del usuario
        $oficinaId = DB::table('users')
            ->where('id', $usuario->id)
            ->value('oficina_id');

        // Obtener nombre de la oficina (si existe)
        $nombreOficinaUsuario = null;
        if ($oficinaId) {
            $nombreOficinaUsuario = Oficina::where('oficina_id', $oficinaId)->value('nombre');
        }

        $pdf = Pdf::loadView('pdf.reporte-rutas', [
            'rutas' => $rutas,
            'oficinas' => $this->oficinas,
            'oficinasConEstado' => $this->oficinasConEstado,
            'nombreOficinaUsuario' => $nombreOficinaUsuario,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_rutas.pdf');
    }

    public function render()
    {
        $rutas = $this->getRutasFiltradas();

        return view('livewire.rutas.rutas-locales', [
            'rutas' => $rutas,
        ]);
    }
}
