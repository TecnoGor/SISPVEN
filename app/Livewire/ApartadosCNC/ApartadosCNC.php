<?php

namespace App\Livewire\ApartadosCNC;

use App\Models\Oficina;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\RegistroApartado;
use App\Models\CodigoApartadoPostal;
use App\Exports\ApartadosCNCExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


#[Layout('layouts.app')]
class ApartadosCNC extends Component
{
    use WithPagination;

    public $search = '';
    public $fecha_restante;
    public $condicion;
    public $estatus;
    public $cantidad;
    public $modalOpen;
    public $apartados_postales = [];
    public $dias_transcurridos = [];
    public $dias_faltantes = [];
    public $usuario;
    public $codigo_ubicacion;
    public $perPage = 25;
    public $desde;
    public $hasta;
    public $oficina_selec;
    public $oficinas_encon = [];

    public function mount()
    {
        $this->usuario = auth()->user();
        $oficina = $this->usuario['oficina_id'];
        $this->codigo_ubicacion = Oficina::where('oficina_id', $oficina)->pluck('codigo_ubicacion')->first();

        $this->apartados_postales = CodigoApartadoPostal::where('apartado', 'LIKE', $this->codigo_ubicacion . '%')
            ->where('oficina_id', $this->usuario['oficina_id'])
            ->orderBy('codigo_apartado_id', 'asc')->get();

        $this->oficinas_encon = Oficina::whereHas('apartado')->get();

        $apartados_activos = RegistroApartado::where('activo', true)->get();
        $apartados_activos->toArray();

        foreach ($apartados_activos as $apartado) {
            // Asegúrate de que el campo sea 'created_at' (en minúsculas y con guion bajo)
            $dias_transcurridos = Carbon::parse($apartado->created_at)->diffInDays(Carbon::now());

            $dias_del_año = Carbon::now()->isLeapYear() ? 366 : 365;

            // Calcula los días faltantes para cumplir el año
            $this->dias_faltantes[$apartado->codigo_apartado_id] = $dias_del_año - $dias_transcurridos;

            // Si deseas almacenar todos los resultados en un arreglo
            $dias_transcurridos_array[$apartado->codigo_apartado_id] = $dias_transcurridos;
            $this->dias_transcurridos = $dias_transcurridos_array;
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage(); // Restablece la página actual a la primera
    }

    public function exportarApartadosCNC()
    {
        // Obtener los datos con los mismos filtros que se aplican en render()
        $apartados_export = $this->obtenerDatosParaExport();

        // Preparar información de filtros para el export
        $filtros = [
            'search' => $this->search,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'oficina_nombre' => null
        ];

        // Obtener nombre de la oficina si está seleccionada
        if ($this->oficina_selec) {
            $oficina = Oficina::find($this->oficina_selec);
            $filtros['oficina_nombre'] = $oficina ? $oficina->nombre : null;
        }

        // Generar nombre del archivo con fecha y hora
        $fecha_actual = Carbon::now()->format('Y-m-d_H-i-s');
        $nombre_archivo = "apartados_cnc_{$fecha_actual}.xlsx";

        return Excel::download(new ApartadosCNCExport($apartados_export, $this->dias_faltantes, $filtros), $nombre_archivo);
    }

    private function obtenerDatosParaExport()
    {
        if ($this->oficina_selec) {
            return CodigoApartadoPostal::where('oficina_id', $this->oficina_selec)
                ->when($this->search, function ($query) {
                    $query->where('apartado', 'LIKE', '%' . $this->search . '%')
                        ->orWhereHas('registro_apartado', function ($query) {
                            $query->where('documento', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('nombre', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('apellido', 'LIKE', '%' . $this->search . '%');
                        });
                })
                ->when($this->desde, function ($query) {
                    $query->whereDate('created_at', '>=', $this->desde);
                })
                ->when($this->hasta, function ($query) {
                    $query->whereDate('created_at', '<=', $this->hasta);
                })
                ->orderBy('codigo_apartado_id', 'asc')
                ->get();
        } else {
            // Si no hay oficina seleccionada, exportar todos los apartados de la oficina del usuario
            $oficina_usuario = $this->usuario['oficina_id'];
            return CodigoApartadoPostal::where('oficina_id', $oficina_usuario)
                ->when($this->search, function ($query) {
                    $query->where('apartado', 'LIKE', '%' . $this->search . '%')
                        ->orWhereHas('registro_apartado', function ($query) {
                            $query->where('documento', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('nombre', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('apellido', 'LIKE', '%' . $this->search . '%');
                        });
                })
                ->when($this->desde, function ($query) {
                    $query->whereDate('created_at', '>=', $this->desde);
                })
                ->when($this->hasta, function ($query) {
                    $query->whereDate('created_at', '<=', $this->hasta);
                })
                ->orderBy('codigo_apartado_id', 'asc')
                ->get();
        }
    }

    public function exportarPdf()
    {
        // Prioriza la oficina seleccionada, si no existe usa la del usuario
        $oficina_id = $this->oficina_selec ?: ($this->usuario ? $this->usuario['oficina_id'] : null);

        // Obtener nombre de la oficina seleccionada
        $oficinaNombre = null;
        if ($oficina_id) {
            $oficina = Oficina::find($oficina_id);
            $oficinaNombre = $oficina ? $oficina->nombre : null;
        }

        // Filtrar apartados por oficina y aplicar filtros
        $apartados = CodigoApartadoPostal::where('oficina_id', $oficina_id)
            ->when($this->search, function ($query) {
                $query->where('apartado', 'LIKE', '%' . $this->search . '%')
                    ->orWhereHas('registro_apartado', function ($query) {
                        $query->where('documento', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('nombre', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('apellido', 'LIKE', '%' . $this->search . '%');
                    });
            })
            ->when($this->desde, function ($query) {
                $query->whereDate('created_at', '>=', $this->desde);
            })
            ->when($this->hasta, function ($query) {
                $query->whereDate('created_at', '<=', $this->hasta);
            })
            ->orderBy('codigo_apartado_id', 'asc')
            ->get();

        // Asocia los días faltantes a cada apartado
        foreach ($apartados as $ap) {
            $ap->dias_faltantes = $this->dias_faltantes[$ap->codigo_apartado_id] ?? '--';
        }

        $pdf = Pdf::loadView('pdf.reporte-apartados-cnc', [
            'apartados' => $apartados,
            'oficinaNombre' => $oficinaNombre,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte-apartados-CNC-' . now()->format('Ymd_His') . '.pdf');
    }

    public function render()
    {
        if ($this->oficina_selec) {
            $apartadosp = CodigoApartadoPostal::where('oficina_id', $this->oficina_selec)
                ->when($this->search, function ($query) {
                    $query->where('apartado', 'LIKE', '%' . $this->search . '%')
                        ->orWhereHas('registro_apartado', function ($query) {
                            $query->where('documento', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('nombre', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('apellido', 'LIKE', '%' . $this->search . '%');
                        });
                })
                ->when($this->desde, function ($query) {
                    $query->whereDate('created_at', '>=', $this->desde);
                })
                ->when($this->hasta, function ($query) {
                    $query->whereDate('created_at', '<=', $this->hasta);
                })
                ->orderBy('codigo_apartado_id', 'asc')
                ->paginate($this->perPage);
        } else {
            // Crear un paginador vacío si oficina_selec no tiene valor
            $apartadosp = new LengthAwarePaginator([], 0, $this->perPage, 1);
        }

        return view('livewire.apartados-c-n-c.apartados-c-n-c', compact('apartadosp'));
    }
}
