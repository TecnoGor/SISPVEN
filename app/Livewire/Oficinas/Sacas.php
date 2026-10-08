<?php

namespace App\Livewire\Oficinas;

use App\Exports\SacasExport;
use App\Livewire\Forms\Oficinas\Createform;
use App\Models\Estado;
use App\Models\Oficina;
use App\Models\Saca;
use App\Models\TipoSaca;
use App\Models\UsuarioSeguimiento;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class Sacas extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;
    public $sortBy = 'saca_id';
    public $sortDir = 'desc';
    public $fechaBusqueda;

    /**
     * Pestaña activa: 'abiertas' | 'cerradas' | 'creadas'.
     *
     * Abiertas y Cerradas listan las valijas que están FÍSICAMENTE en la oficina
     * (según el último encaminamiento). Creadas lista las que nacieron aquí,
     * estén donde estén ahora — es el filtro histórico, útil para rastrear una
     * valija ya despachada.
     */
    public $pestana = 'abiertas';

    public Createform $createForm;
    public $estadoSeleccionado = null;
    public $oficinas = [];
    public $filteredOficinas = [];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFechaBusqueda()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedEstadoSeleccionado($estadoId)
    {
        if (empty($estadoId)) {
            $this->filteredOficinas = [];
        } else {
            $this->filteredOficinas = Oficina::where('estado_id', $estadoId)
                ->where('estatus_id', 1)
                ->whereNot('externa', true)
                ->get();
        }
        $this->createForm->oficinaSeleccionada = null;
    }

    public function mostrarAbiertas()
    {
        $this->pestana = 'abiertas';
        // Volver a la primera página al cambiar de pestaña, para evitar quedar
        // en una página que no existe en el listado nuevo.
        $this->resetPage();
    }

    public function mostrarCerradasValijas()
    {
        $this->pestana = 'cerradas';
        $this->resetPage();
    }

    public function mostrarCreadas()
    {
        $this->pestana = 'creadas';
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->fechaBusqueda = null;
        $this->resetPage();
    }

    public function create()
    {
        $this->resetValidation();
        $this->createForm->create();
    }

    public function store()
    {
        $this->validate([
            'createForm.tipoSeleccionado' => 'required',
            'estadoSeleccionado' => 'required',
            'createForm.oficinaSeleccionada' => 'required',
        ], [
            'createForm.tipoSeleccionado.required' => 'Debe seleccionar un tipo de valija.',
            'estadoSeleccionado.required' => 'Debe seleccionar un estado.',
            'createForm.oficinaSeleccionada.required' => 'Debe seleccionar una oficina de destino.',
        ]);

        $this->createForm->store();
        $this->estadoSeleccionado = null;
        $this->filteredOficinas = [];
        $this->dispatch('alertSuccess', message: 'Valija creada exitosamente!');
    }

    /**
     * Query base de los tres listados.
     *
     * Antes esta consulta estaba duplicada en render(), exportarExcel() y
     * exportarPDF(), lo que obligaba a mantener cualquier cambio de filtro por
     * triplicado.
     *
     * Las pestañas Abiertas y Cerradas usan `ubicadaEn()`, que resuelve la
     * ubicación desde el último encaminamiento: la oficina ve lo que tiene
     * físicamente, no lo que creó. La pestaña Creadas conserva el filtro
     * histórico por `sacas.oficina_id`.
     */
    private function sacasQuery()
    {
        $usuario = auth()->user();

        $query = Saca::query();

        if ($this->pestana === 'creadas') {
            // Histórico: nacieron aquí, estén donde estén ahora. No se filtra
            // por `cerrado` porque el interés es rastrear la valija completa.
            $query->where('oficina_id', $usuario->oficina_id);
        } else {
            $query->ubicadaEn($usuario->oficina_id)
                ->where('cerrado', $this->pestana === 'cerradas');
        }

        return $query
            ->when(
                $this->fechaBusqueda,
                fn($q) => $q->whereDate('created_at', $this->fechaBusqueda)
            )
            ->where(function ($q) {
                $q->where('codigo_saca', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDir);
    }

    public function reabrirValija($sacaId)
    {
        // La valija debe estar FÍSICAMENTE en la oficina del usuario. Antes se
        // hacía un Saca::find() sin más: la única protección era que el botón
        // solo se pintaba en el listado ya filtrado, pero una petición Livewire
        // manipulada podía reabrir cualquier valija del sistema.
        //
        // ubicadaEn() excluye además las EN TRÁNSITO (no están en ninguna
        // oficina) y las ABIERTAS (llegaron a una OPT y no se reutilizan).
        $saca = Saca::query()
            ->ubicadaEn(auth()->user()->oficina_id)
            ->where('sacas.saca_id', $sacaId)
            ->first();

        if (!$saca) {
            $this->dispatch('alertError', message: 'Valija no encontrada o no se encuentra en su oficina.');
            return;
        }

        if (!$saca->cerrado) {
            $this->dispatch('alertError', message: 'La valija ya está abierta.');
            return;
        }

        $saca->cerrado = false;
        $saca->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se reabrió la valija ({$saca->saca_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Valija reabierta con éxito.');
    }

    public function render()
    {
        $usuario = auth()->user();

        //$tipos = TipoSaca::select('tipo_saca_id', 'nombre')->get();

        // Filtrar tipos según el rol
        if ($usuario->hasRole('Clasificador EMS')) {
            $tipos = TipoSaca::where('tipo_saca_id', 12)->where('activo', true)->get();
        } elseif ($usuario->hasRole('Clasificador Bultos')) {
            $tipos = TipoSaca::where('tipo_saca_id', 15)->where('activo', true)->get();
        } else {
            $tipos = TipoSaca::where('activo', true)->get();
        }


        $estados = Estado::select('estado_id', 'nombre')->get();

        // Aquí estamos usando paginación en Livewire, que maneja los resultados automáticamente.
        $sacas = $this->sacasQuery()->paginate($this->perPage);

        return view('livewire.oficinas.sacas', [
            'sacas' => $sacas, // Aquí se pasa la paginación correctamente
            'tipos' => $tipos,
            'estados' => $estados,
            'oficinas' => $this->filteredOficinas,
        ]);
    }

    public function exportarExcel()
    {
        $usuario = auth()->user();
        $oficina = $usuario->oficina;
        $sacas = $this->sacasQuery()->get();

        return Excel::download(new SacasExport($sacas, $this->pestana, $usuario, $oficina), 'valijas.xlsx');
    }

    public function exportarPDF()
    {
        $usuario = auth()->user();

        $sacas = $this->sacasQuery()->paginate($this->perPage); // Aquí se maneja la paginación también.

        $pdf = Pdf::loadView('pdf.sacas-reporte', [
            'sacas' => $sacas,
            'usuario' => $usuario
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'valijas_reporte.pdf');
    }
}
