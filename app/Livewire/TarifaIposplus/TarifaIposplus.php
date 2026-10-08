<?php

namespace App\Livewire\TarifaIposplus;

use Livewire\Component;
use App\Models\TarifaIposplus as ModeloTarifa;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use Livewire\WithPagination; 

#[Layout('layouts.app')]
class TarifaIposplus extends Component
{
    use WithPagination; 
    public $search = ''; 
    public $perPage = 5;
    public $sortBy = 'tarifa_iposplus_id';
    public $sortDir = 'DESC';
    public $modal_open = false;
    public $editing_id = null;
    public $kilo_min, $kilo_max, $precio, $esta_activo = true;

    protected $rules = [
        'kilo_min' => 'required|numeric|min:0',
        'kilo_max' => 'required|numeric|gt:kilo_min',
        'precio'   => 'required|numeric|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create() {
        $this->reset_campos();
        $this->modal_open = true;
    }

    public function edit($id) {
        $registro = ModeloTarifa::findOrFail($id);
        $this->editing_id = $id;
        $this->kilo_min = $registro->kilo_min;
        $this->kilo_max = $registro->kilo_max;
        $this->precio = $registro->precio;
        $this->esta_activo = (bool)$registro->activo;
        $this->modal_open = true;
    }

    public function save()
    {
        // Limpiar formato de moneda (1.234,56 → 1234.56)
        $this->precio = str_replace(['.', ','], ['', '.'], $this->precio);

        $this->validate();

        if ($this->kilo_max <= $this->kilo_min) {
            $this->dispatch('alertSuccess2', message: 'El Kilo Máximo debe ser mayor al Kilo Mínimo');
            return;
        }

        if ($this->esta_activo) {
            $conflicto = $this->buscarSolapamiento($this->kilo_min, $this->kilo_max, $this->editing_id);
            if ($conflicto) {
                $this->dispatch('alertSuccess2', message: "No se pudo guardar: el rango se solapa con la tarifa #{$conflicto->tarifa_iposplus_id} ({$conflicto->kilo_min} - {$conflicto->kilo_max} Kg)");
                return;
            }
        }

        $tarifa = $this->editing_id ? ModeloTarifa::findOrFail($this->editing_id) : new ModeloTarifa();

        $era_edicion = (bool) $this->editing_id;

        $tarifa->kilo_min = $this->kilo_min;
        $tarifa->kilo_max = $this->kilo_max;
        $tarifa->precio   = $this->precio;
        $tarifa->activo   = $this->esta_activo ? true : false;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => $era_edicion ? 'update' : 'create',
            'descripcion' => ($era_edicion ? "Se actualizó" : "Se creó") . " la tarifa Iposplus ({$tarifa->tarifa_iposplus_id}) rango {$tarifa->kilo_min}-{$tarifa->kilo_max} Kg",
        ]);

        $this->modal_open = false;
        $mensaje = $era_edicion ? 'Tarifa actualizada con éxito' : 'Tarifa registrada con éxito';
        $this->dispatch('alertSuccess', message: $mensaje);
        $this->reset_campos();
    }

    public function toggleActivo($id) {
        $tarifa = ModeloTarifa::findOrFail($id);

        if (!$tarifa->activo) {
            $conflicto = $this->buscarSolapamiento($tarifa->kilo_min, $tarifa->kilo_max, $tarifa->tarifa_iposplus_id);
            if ($conflicto) {
                $this->dispatch('alertSuccess2', message: "No se pudo activar: el rango se solapa con la tarifa #{$conflicto->tarifa_iposplus_id} ({$conflicto->kilo_min} - {$conflicto->kilo_max} Kg)");
                return;
            }
        }

        $tarifa->activo = !$tarifa->activo;
        $tarifa->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se cambió el estatus de la tarifa Iposplus ({$tarifa->tarifa_iposplus_id}) a " . ($tarifa->activo ? 'activa' : 'inactiva'),
        ]);

        $this->dispatch('alertSuccess', message: 'Estado del registro actualizado');
    }

    private function buscarSolapamiento($kilo_min, $kilo_max, $excluir_id = null)
    {
        return ModeloTarifa::where('activo', true)
            ->where('kilo_min', '<=', $kilo_max)
            ->where('kilo_max', '>=', $kilo_min)
            ->when($excluir_id, fn($q) => $q->where('tarifa_iposplus_id', '!=', $excluir_id))
            ->first();
    }

    public function setSortBy($column) {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'ASC' ? 'DESC' : 'ASC';
        } else {
            $this->sortBy = $column;
            $this->sortDir = $column === 'activo' ? 'DESC' : 'ASC';
        }
        $this->resetPage();
    }

    public function reset_campos() {
        $this->reset(['kilo_min', 'kilo_max', 'precio', 'esta_activo', 'editing_id']);
    }

    public function render() {
        $lista_tarifas = ModeloTarifa::query()
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('precio', 'like', '%' . $this->search . '%')
                      ->orWhere('kilo_min', 'like', '%' . $this->search . '%')
                      ->orWhere('kilo_max', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->sortBy !== 'activo', fn($q) => $q->orderBy('activo', 'DESC'))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.tarifa-iposplus.tarifa-iposplus', compact('lista_tarifas'));
    }
}