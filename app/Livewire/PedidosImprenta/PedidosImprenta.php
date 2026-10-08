<?php

namespace App\Livewire\PedidosImprenta;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PedidoImprenta;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PedidosImprenta extends Component
{
    use WithPagination;

    public $oficina;
    public $usuario = [];

    // Nuevas propiedades para búsqueda, paginación y historial
    public $search = '';
    public $perPage = 10;
    public $showHistory = false; // false -> muestra activos (por hacer). true -> muestra completados (historial)

    // Mantener query string para preservar estado al paginar / compartir URL (opcional)
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'showHistory' => ['except' => false],
    ];

    public function mount()
    {
        $this->usuario = auth()->user();
    }

    // Resetea la página al cambiar la búsqueda o la paginación
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // Alterna entre vista activa / historial
    public function toggleHistory()
    {
        $this->showHistory = ! $this->showHistory;
        $this->resetPage();
    }

    public function estatus($id)
    {
        $pedido = PedidoImprenta::where('pedido_imprenta_id', $id)->first();

        $pedido->activo = !$pedido->activo;
        $pedido->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se cambió el estatus del pedido de imprenta ({$pedido->pedido_imprenta_id}) a " . ($pedido->activo ? 'activo' : 'completado'),
        ]);

        $this->dispatch('alertSuccess', message: 'Estatus del pedido actualizado correctamente');
    }

    public function render()
    {
        $query = PedidoImprenta::where('oficina_id', $this->usuario['oficina_id']);

        // Si esta en historial, mostrar los completados (activo = false), si no, los activos (true)
        if ($this->showHistory) {
            $query->where('activo', false);
        } else {
            $query->where('activo', true);
        }

        // Filtro de búsqueda
        if (!empty($this->search)) {
            $like = '%' . $this->search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('nombre', 'like', $like)
                  ->orWhere('apellido', 'like', $like)
                  ->orWhere('documento', 'like', $like)
                  ->orWhere('nombre_original', 'like', $like);
            });
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.pedidos-imprenta.pedidos-imprenta', ['pedidos' => $pedidos]);
    }
}