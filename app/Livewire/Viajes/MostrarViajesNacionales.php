<?php

namespace App\Livewire\Viajes;

use Carbon\Carbon;
use App\Models\Viaje;
use App\Models\Oficina;
use Livewire\Component;
use App\Models\Vehiculo;
use App\Models\Proveedor;
use App\Models\RutasModelo;
use Livewire\WithPagination;
use App\Models\OficinaVehiculo;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ViajesNacionalesExport;
use App\Livewire\Forms\Viajes\EditForm;
use App\Livewire\Forms\Viajes\CreateForm3;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')]
class MostrarViajesNacionales extends Component
{
    use WithPagination;

    public $search;
    public $filter = 'all';
    public $perPage = 5;
    public $sortDir = 'ASC';
    public $sortBy = 'ruta_id';
    public CreateForm3 $createForm;
    public EditForm $editForm;
    public $rutas;
    public $proveedores = [];
    public $vehiculos = [];
    public $trips = [];
    public $filteredProveedores = [];
    public $week;

    public function mount()
    {
        $this->proveedores = Proveedor::all();
        $this->rutas = RutasModelo::where('activo', true)->get();

        $this->trips[] = [
            'dia_semana' => '',
            'ruta_id' => '',
            'vehiculo_id' => '',
            'vehiculos_disponibles' => [],
        ];
    }

    public function resetFields()
    {
        $this->proveedores = [];
        $this->vehiculos = [];
        $this->trips = [];
    }

    public function addAnotherTrip()
    {
        $this->trips[] = [
            'dia_semana' => null,
            'ruta_id' => null,
            'vehiculo_id' => null,
            'vehiculos_disponibles' => [],
        ];
    }

    public function updatedTrips($value, $name)
    {
        $matches = [];
        preg_match('/trips\.(\d+)\.ruta_id/', $name, $matches);

        if (isset($matches[1])) {
            $index = $matches[1];
            $rutaId = $this->trips[$index]['ruta_id'];

            if ($rutaId) {
                $oficinaOrigenId = RutasModelo::where('ruta_id', $rutaId)
                    ->value('oficina_id_origen');

                $this->trips[$index]['vehiculos_disponibles'] = $oficinaOrigenId
                    ? Vehiculo::where('oficina_id', $oficinaOrigenId)->get()
                    : [];
            }
        }
    }

    public function removeTrip($index)
    {
        unset($this->trips[$index]);
        $this->trips = array_values($this->trips);
    }

    public function updatedCreateFormRuta($rutaId)
    {
        $this->resetFields();
        $oficinaOrigenId = RutasModelo::where('ruta_id', $rutaId)
            ->value('oficina_id_origen');

        if ($oficinaOrigenId) {
            $idsPivote = OficinaVehiculo::where('oficina_id', $oficinaOrigenId)
                ->pluck('vehiculo_id');

            $idsDirect = Vehiculo::where('oficina_id', $oficinaOrigenId)
                ->where('Activo', true)
                ->pluck('vehiculo_id');

            $this->vehiculos = Vehiculo::whereIn('vehiculo_id', $idsPivote->merge($idsDirect)->unique())
                ->where('Activo', true)
                ->get();
        } else {
            $this->vehiculos = collect();
        }
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->createForm['open'] = false;
    }

    public function updatedCreateFormProveedorId($proveedor_id)
    {
        $this->vehiculos = Vehiculo::where('proveedor_id', $proveedor_id)->get();
    }

    public function desactivar(Viaje $viaje)
    {
        $viaje->activo = false;
        $viaje->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el viaje nacional ({$viaje->viaje_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Viaje Desactivado exitosamente!');
    }

    public function activar(Viaje $viaje)
    {
        $viaje->activo = true;
        $viaje->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó el viaje nacional ({$viaje->viaje_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Viaje Activado exitosamente!');
    }

    public function edit(Viaje $viaje)
    {
        $this->resetValidation();
        $this->editForm->edit($viaje);
    }

    public function update()
    {
        $this->editForm->update();
        $this->dispatch('alertSuccess', message: 'Viaje actualizado exitosamente!');
    }

    public function create()
    {
        $this->resetValidation();
        $this->trips = [];
        $this->createForm->create();
    }

    public function store()
    {
        $this->validate();
        $this->createForm->trips = $this->trips;
        $this->createForm->store();
        $this->dispatch('alertSuccess', message: 'Viajes creados exitosamente!');
    }

    public function updatedWeek($value)
    {
        $this->resetPage();
    }

    protected function getWeekRange($week)
    {
        $year = substr($week, 0, 4);
        $weekNumber = substr($week, 6);

        $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        return [$startOfWeek, $endOfWeek];
    }

    public function generateViajeReportPDF()
    {
        $weekRange = $this->week ? $this->getWeekRange($this->week) : [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ];

        $usuario = auth()->user();

        $viajes = Viaje::when($usuario->hasRole('Gerente de Estado'), function ($query) use ($usuario) {
            $estadoId = DB::table('usuario_estados')
                ->where('id_user', $usuario->id)
                ->value('id_estado');

            $vehiculoIds = Vehiculo::whereHas('oficina', function ($query) use ($estadoId) {
                $query->where('estado_id', $estadoId)
                    ->where('tipo_oficina_id', 4);
            })->pluck('vehiculo_id');

            $query->whereIn('vehiculo_id', $vehiculoIds);
        })
            ->when($usuario->hasRole(['SuperAdmin', 'Operaciones Nacionales']), function ($query) {
                $query->whereHas('ruta.oficinaDestino', function ($query) {
                    $query->whereIn('tipo_oficina_id', [1, 2, 3]);
                });
            })
            ->when($this->search, function ($query) {
                $query->where('codigo', 'LIKE', '%' . $this->search . '%');
            })
            ->whereBetween('fecha_salida', $weekRange)
            ->orderBy($this->sortBy, $this->sortDir)
            ->get();

        $oficinaId = DB::table('users')
            ->where('id', $usuario->id)
            ->value('oficina_id');

        $nombreOficinaUsuario = $oficinaId
            ? Oficina::where('oficina_id', $oficinaId)->value('nombre')
            : null;

        $pdf = Pdf::loadView('pdf.viaje_nacional_report', [
            'viajes' => $viajes,
            'nombreOficinaUsuario' => $nombreOficinaUsuario,
            'semana' => $this->week
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_viajes.pdf');
    }

    public function render()
    {
        $rutas = RutasModelo::where('activo', true)
            ->when($this->search, function ($query) {
                $query->where('ruta', 'LIKE', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $weekRange = $this->week ? $this->getWeekRange($this->week) : [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ];

        $viajes = Viaje::when($this->search, function ($query) {
            $query->where('tipo_oficina_id', 4);
        })
            ->whereHas('vehiculo.oficina', function ($query) {
                $query->whereNotNull('oficina_id');
            })
            ->whereBetween('fecha_salida', $weekRange)
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $oficina_id = auth()->user()->oficina_id;

        return view('livewire.viajes.mostrar-viajes-nacionales', [
            'oficina_id' => $oficina_id,
            'viajes' => $viajes,
            'rutas' => $rutas,
            'vehiculos' => $this->vehiculos,
        ]);
    }


    public function exportViajesToExcel()
    {
        $usuario = auth()->user();
        $export = new ViajesNacionalesExport($this->week, $this->search, $usuario);

        return Excel::download($export, 'viajes_nacionales.xlsx');
    }
}
