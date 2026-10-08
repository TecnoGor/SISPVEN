<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Envio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use App\Exports\AduanaAlmacenExport;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class AduanaAlmacen extends Component
{
    public $mostrarEntregados = false;
    public $search = '';

    public function toggleEntregados()
    {
        $this->mostrarEntregados = !$this->mostrarEntregados;
    }

    public function getEnviosProperty()
    {
        $usuario = Auth::user();
        $oficinaId = $usuario->oficina_id;

        return Envio::whereHas('almacenAduana', function ($query) use ($oficinaId) {
                $query->where('oficina_id', $oficinaId)
                      ->where('estatus', $this->mostrarEntregados ? false : true);
            })
            ->where('codigo_envio', 'like', '%' . $this->search . '%')
            ->with('almacenAduana')
            ->get();
    }

public function generateEnviosPDF()
{
    $usuario = auth()->user();
    $oficinaId = $usuario->oficina_id;

    $envios = Envio::whereHas('almacenAduana', function ($query) use ($oficinaId) {
        $query->where('oficina_id', $oficinaId)
              ->where('estatus', $this->mostrarEntregados ? false : true);
    })
    ->where('codigo_envio', 'like', '%' . $this->search . '%')
    ->with('almacenAduana')
    ->get();

    $titulo = $this->mostrarEntregados ? 'Reporte de Envíos Entregados' : 'Reporte de Envíos Disponibles en Almacén';

    $pdf = Pdf::loadView('pdf.reporte-envios', [
        'envios' => $envios,
        'titulo' => $titulo,
        'oficina' => \App\Models\Oficina::find($oficinaId),
    ]);

    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->stream();
    }, 'reporte_envios.pdf');
}

public function exportToExcel()
{
    $usuario = auth()->user();
    $oficinaId = $usuario->oficina_id;

    $envios = Envio::whereHas('almacenAduana', function ($query) use ($oficinaId) {
        $query->where('oficina_id', $oficinaId)
              ->where('estatus', $this->mostrarEntregados ? false : true);
    })
    ->where('codigo_envio', 'like', '%' . $this->search . '%')
    ->with('almacenAduana')
    ->get();

    $titulo = $this->mostrarEntregados
        ? 'Reporte de Envíos Entregados de Aduana'
        : 'Reporte de Envíos Disponibles en Almacén de Aduana';

    return Excel::download(
        new AduanaAlmacenExport($envios, $titulo),
        'reporte_almacen_' . now()->format('Ymd_His') . '.xlsx'
    );
}


    public function render()
    {
        return view('livewire.aduana-almacen', [
            'envios' => $this->envios,
            'mostrarEntregados' => $this->mostrarEntregados,
        ]);
    }
}
