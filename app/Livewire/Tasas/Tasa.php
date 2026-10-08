<?php

namespace App\Livewire\Tasas;

use Livewire\Component;
use App\Models\Parametro;
use App\Models\ParametroHistorico;
use App\Models\UsuarioSeguimiento;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\Tasas\EditForm;
use Carbon\Carbon;

#[Layout('layouts.app')]
class Tasa extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public EditForm $editForm;

    // — Modal crear divisa —
    public $modal_crear = false;
    public $nombre_nueva;
    public $valor_nueva;

    // — Modal historial —
    public $modal_historial = false;
    public $historial_nombre = '';
    public $filtro_desde = '';
    public $filtro_hasta = '';
    public $historial_parametro_id = null;
    public $historialPerPage = 15;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function edit(Parametro $tasa)
    {
        $this->resetValidation();
        $this->editForm->edit($tasa);
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Tasa editada exitosamente!');
    }

    public function abrirCrear()
    {
        $this->resetValidation();
        $this->nombre_nueva = '';
        $this->valor_nueva = '';
        $this->modal_crear = true;
    }

    public function cerrarCrear()
    {
        $this->modal_crear = false;
        $this->nombre_nueva = '';
        $this->valor_nueva = '';
    }

    public function crearDivisa()
    {
        // Limpiar formato bancario para guardar en base de datos
        // Ej: "1.234,56" -> "1234.56"
        $this->valor_nueva = str_replace('.', '', $this->valor_nueva);
        $this->valor_nueva = str_replace(',', '.', $this->valor_nueva);

        $this->validate([
            'nombre_nueva' => 'required|string|max:14|unique:parametro,nombre',
            'valor_nueva'  => 'required|numeric|min:0',
        ], [
            'nombre_nueva.required' => 'El nombre es obligatorio.',
            'nombre_nueva.unique'   => 'Ya existe una divisa con ese nombre.',
            'nombre_nueva.max'      => 'El nombre no puede tener más de 14 caracteres.',
            'valor_nueva.required'  => 'El valor es obligatorio.',
            'valor_nueva.numeric'   => 'El valor debe ser numérico.',
            'valor_nueva.min'       => 'El valor no puede ser negativo.',
        ]);

        $nueva_divisa = Parametro::create([
            'nombre' => $this->nombre_nueva,
            'valor'  => $this->valor_nueva,
            'activo' => true,
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se creó la divisa ({$nueva_divisa->parametro_id}) '{$this->nombre_nueva}' con valor {$this->valor_nueva}",
        ]);

        $this->dispatch('alertSuccess', message: 'Divisa creada exitosamente!');
        $this->cerrarCrear();
    }

    public function toggleActivo($parametro_id)
    {
        $tasa = Parametro::find($parametro_id);

        if (!$tasa) {
            $this->dispatch('alertError', message: 'Divisa no encontrada.');
            return;
        }

        $tasa->activo = !$tasa->activo;
        $tasa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se " . ($tasa->activo ? 'activó' : 'desactivó') . " la divisa ({$tasa->parametro_id}) '{$tasa->nombre}'",
        ]);

        $estado = $tasa->activo ? 'activada' : 'desactivada';
        $this->dispatch('alertSuccess', message: "Divisa {$estado} exitosamente!");
    }

    // — Historial —

    public function verHistorial($parametro_id)
    {
        $tasa = Parametro::find($parametro_id);

        if (!$tasa) {
            $this->dispatch('alertError', message: 'Divisa no encontrada.');
            return;
        }

        $this->historial_parametro_id = $parametro_id;
        $this->historial_nombre = $tasa->nombre;
        $this->historialPerPage = 15;

        // Filtro por defecto: semana actual (lunes a domingo)
        $this->filtro_desde = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $this->filtro_hasta = Carbon::now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');

        $this->resetPage('historialPage');
        $this->modal_historial = true;
    }

    public function updatedFiltroDesde()
    {
        $this->resetPage('historialPage');
    }

    public function updatedFiltroHasta()
    {
        $this->resetPage('historialPage');
    }

    public function updatedHistorialPerPage()
    {
        $this->resetPage('historialPage');
    }

    public function limpiarFiltros()
    {
        $this->filtro_desde = '';
        $this->filtro_hasta = '';
        $this->resetPage('historialPage');
    }

    public function cerrarHistorial()
    {
        $this->modal_historial = false;
        $this->filtro_desde = '';
        $this->filtro_hasta = '';
        $this->historial_parametro_id = null;
        $this->historial_nombre = '';
        $this->historialPerPage = 15;
    }

    public function render()
    {
        $tasas = Parametro::query()
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orderBy('parametro_id', 'asc')
            ->paginate($this->perPage);

        // Historial paginado (solo si el modal está abierto)
        $historial = null;
        if ($this->modal_historial && $this->historial_parametro_id) {
            $query = ParametroHistorico::where('parametro_id', $this->historial_parametro_id)
                ->with('usuario');

            if ($this->filtro_desde) {
                $query->whereDate('fecha_cambio', '>=', $this->filtro_desde);
            }

            if ($this->filtro_hasta) {
                $query->whereDate('fecha_cambio', '<=', $this->filtro_hasta);
            }

            $historial = $query->orderBy('fecha_cambio', 'desc')
                ->paginate($this->historialPerPage, ['*'], 'historialPage');
        }

        return view('livewire.tasas.tasa', compact('tasas', 'historial'));
    }
}