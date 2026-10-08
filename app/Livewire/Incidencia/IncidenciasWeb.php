<?php

namespace App\Livewire\Incidencia;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncidenciasExport;
use App\Models\EnvioIncidencia;
use App\Models\IncidenciaDetalle;
use App\Models\Incidencia;
use App\Models\Envio;


#[Layout('layouts.app')]
class IncidenciasWeb extends Component
{
    public $mostrar_incidencia = false;
    public $envio_incidencia = [];
    public $incidencia = [];
    public $detalles = [];


    public function verIncidencia($incidencia_id)
    {
        $this->envio_incidencia = EnvioIncidencia::where('envio_incidencia_id', $incidencia_id)->first();
        $this->incidencia = IncidenciaDetalle::where('envio_incidencia_id', $this->envio_incidencia['envio_incidencia_id'])->pluck('incidencia_id');
        $this->detalles = Incidencia::whereIn('incidencia_id', $this->incidencia)->get();

        if($this->envio_incidencia){
            $this->mostrar_incidencia = true;
        }
    }

    public function reporte_excel()
    {
        $usuario = auth()->user();
        $incidencias = EnvioIncidencia::where('oficina_id', $usuario->oficina_id)
            ->whereHas('envio_almacen', function ($query) {
                $query->where('estatus', true);
            })->get();

        return Excel::download(new IncidenciasExport($incidencias), 'incidencias.xlsx');
    }

    public function cerrarIncidencia()
    {
        $this->mostrar_incidencia = false;
    }


    public function render()
    {
        $usuario = auth()->user();
        $incidencias = EnvioIncidencia::where('oficina_id', $usuario->oficina_id)
        ->whereHas('envio_almacen', function ($query) {
        $query->where('estatus', true);
        })->get();
        
        // Pasar los envíos a la vista
        return view('livewire.incidencia.incidencias-web',compact('incidencias'));
    }
}
