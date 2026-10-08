<?php

namespace App\Livewire\EnviosPorConfirmar;

use App\Models\Envio;
use App\Models\FacturacionEnvio;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EnviosConfirmar;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class EnviosPorConfirmar extends Component
{
    use WithPagination;

    public $search;
    public $por_pagina = 15;

    // Al cambiar la búsqueda, volver a la primera página para no quedar en
    // una página que el nuevo filtro ya no tiene.
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Al cambiar la cantidad por página, volver a la primera página.
    public function updatingPorPagina()
    {
        $this->resetPage();
    }

    /**
     * Consulta base de envíos por confirmar (sin paginar).
     * La usan tanto render() (paginada) como reporte_excel() (completa).
     */
    protected function baseQuery()
    {
        $usuario = auth()->user();
        $codigo_oficina = $usuario->oficina->codigo;

        // Envíos cuyo último estatus de encaminamiento sigue en 1 (por confirmar).
        return Envio::with('servicio', 'users')->whereIn('servicio_id', [1, 9, 10, 24])
            ->whereHas('envio_encaminamientos', function ($query) {
                // Solo considera los registros con estatus_id = 1
                $query->where('estatus_id', 1);
            })
            ->whereNotExists(function ($query) {
                // Excluye los envíos que ya tienen un estatus posterior a 1.
                // Anti-join correlacionado: evita materializar toda la lista de
                // envio_id de envios_encaminamiento como hacía el whereNotIn.
                $query->select(DB::raw(1))
                    ->from('envios_encaminamiento')
                    ->whereColumn('envios_encaminamiento.envio_id', 'envios.envio_id')
                    ->where('estatus_id', '>', 1);
            })
            ->whereHas('facturacion_envios')
            ->where('codigo_envio', 'LIKE', $codigo_oficina . '%')
            ->where('documento_rem', 'LIKE', '%' . $this->search . '%');
    }

    public function reporte_excel()
    {
        $envios = $this->baseQuery()->get();

        if ($envios->isEmpty()) {
            $this->dispatch('alertSuccess2', message: 'No se encuentra informacion para realizar el reporte!');
            return;
        }
        return Excel::download(new EnviosConfirmar($envios), 'envios-confirmar.xlsx');
    }


    public function generarTermica($envio)
    {
        // Redirige a la ruta 'generar-termica' pasando el 'envio_id'
        return $this->redirect(route('generar-termica', ['envio' => $envio]), navigate: true);
    }


    public function render()
    {
        $envios = $this->baseQuery()->paginate($this->por_pagina);

        return view('livewire.envios-por-confirmar.envios-por-confirmar', [
            'envios' => $envios,
        ]);
    }
}
