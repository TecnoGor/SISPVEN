<?php

namespace App\Livewire\ReciboConsignacion;

use App\Models\Envio;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReciboConsignacion extends Component
{
    use WithPagination;

    public $buscar = '';
    public $input = 10; // elementos por página
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected string $paginationTheme = 'tailwind';

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function updatingInput()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function exportarPdf($envioId)
    {
        return redirect()->route('recibo-consignacion.pdf', ['envioId' => $envioId]);
    }

    public function descargarPdf($envioId)
    {
        return redirect()->route('recibo-consignacion.pdf', ['envioId' => $envioId]);
    }

    public function verDetalles($envioId)
    {
        $envio = Envio::with(['servicio', 'users', 'oficinaOrigen'])->findOrFail($envioId);
        
        // Emitir evento para mostrar modal con detalles
        $this->dispatch('mostrar-detalles-envio', [
            'envio' => $envio,
            'servicio_tipo' => $envio->servicio->servicio_id == 1 ? 'EMS' : 'Estándar'
        ]);
    }

    public function render()
    {
        $usuario = auth()->user();
        $codigo_oficina = $usuario->oficina->codigo;

        $envios = Envio::with('servicio', 'users')
            ->where('oficina_id', $usuario->oficina_id)
            //->where('codigo_envio', 'LIKE', $codigo_oficina . '%')
            ->whereNotIn('servicio_id', [3]) // Filtrar por servicios 9 y 21
            ->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('documento_rem', 'LIKE', '%' . $this->buscar . '%')
                        ->orWhere('codigo_envio', 'LIKE', '%' . $this->buscar . '%')
                        ->orWhere('nombre_rem', 'LIKE', '%' . $this->buscar . '%')
                        ->orWhere('apellido_rem', 'LIKE', '%' . $this->buscar . '%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->input);
        return view('livewire.recibo-consignacion.recibo-consignacion', compact('envios'));
    }
}
