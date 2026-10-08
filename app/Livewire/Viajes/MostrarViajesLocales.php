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
use App\Exports\ViajesExport;
use App\Models\OficinaVehiculo;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Livewire\Forms\Viajes\EditForm;
use App\Livewire\Forms\Viajes\CreateForm;
use App\Livewire\Forms\Viajes\CreateForm2;
use App\Models\UsuarioSeguimiento;

#[Layout('layouts.app')] 
class MostrarViajesLocales extends Component
{ 
    use WithPagination;

    public $week; 
    public $search; // Campo de búsqueda
    public $filter = 'all'; // Filtro por defecto
    public $perPage = 5; // Registros por página
    public $sortDir = 'ASC';
    public $sortBy = 'ruta_id'; // Columna por la que se ordena
    public CreateForm2 $createForm;
    public EditForm $editForm;
    public $rutas;
    public $proveedores = [];
    public $vehiculos = [];
    public $trips = [];
    public $filteredProveedores = [];


    public function mount()
    {
        $this->proveedores = Proveedor::all();
        $this->rutas = RutasModelo::where('activo', true)->get();

        $this->trips[] = [
            'dia_semana' => '',
            'proveedor_id' => '',
            'vehiculo_id' => ''
        ];
    }
    
    public function resetFields()
    {
        $this->proveedores = [];
        $this->vehiculos = [];
        $this->trips = []; // Si quieres resetear también los viajes
    }
    public function addAnotherTrip()
    {
        $this->trips[] = [
            'dia_semana' => null,
            'proveedor_id' => null,
            'vehiculo_id' => null,
        ];
    }


    public function removeTrip($index)
    {
        unset($this->trips[$index]);
        $this->trips = array_values($this->trips); // Re-indexa el arreglo para evitar problemas en el bucle
    }

    public function updatedCreateFormRuta($rutaId)
    {
        $this->resetFields();

        $this->proveedores = Proveedor::whereHas('rutas', function ($query) use ($rutaId) {
            $query->where('proveedor_ruta.ruta_id', $rutaId);
        })->get();

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
        $this->createForm['open'] = false; // Cierra el modal
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
            'descripcion' => "Se desactivó el viaje local ({$viaje->viaje_id})",
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
            'descripcion' => "Se activó el viaje local ({$viaje->viaje_id})",
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
        $this->trips = []; // Asegúrate de que esté limpio al empezar
        $this->createForm->create(); // Llama el método create() de CreateForm
    }
    
    public function store()
    {
        $this->validate();
        $this->createForm->trips = $this->trips;
        
        $this->createForm->store(); // Llama a la función 'store' para almacenar los datos
        $this->dispatch('alertSuccess', message: 'Viajes creados exitosamente!');
    }
    

    public function updatedWeek($value)
    {
        $this->resetPage(); // Resetea la paginación al cambiar la semana seleccionada.
    }

    protected function getWeekRange($week)
    {
        $year = substr($week, 0, 4);
        $weekNumber = substr($week, 6);

        $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        return [$startOfWeek, $endOfWeek];
    }

    public function render()
    {
        $rutaas = RutasModelo::when(auth()->user()->hasRole('Gerente de Estado'), function ($query) {
            $usuario = auth()->user();
        
            // Obtener el estado_id asociado al usuario
            $estadoId = DB::table('usuario_estados')
                ->where('id_user', $usuario->id)
                ->value('id_estado');
        
            // Filtrar las oficinas de origen basadas en el estado_id
            $oficinasOrigenIds = Oficina::where('estado_id', $estadoId)
                ->pluck('oficina_id'); // Obtener los IDs de las oficinas de origen
        
            // Filtrar las rutas que coincidan con las oficinas de origen
            $query->whereIn('oficina_id_origen', $oficinasOrigenIds);
        })
        ->when(auth()->user()->hasRole(['SuperAdmin', 'Operaciones Nacionales']), function ($query) {
            // No aplicamos filtros adicionales porque deben mostrarse todas las rutas
        })
        ->when($this->search, function ($query) {
            $query->where('ruta', 'LIKE', '%' . $this->search . '%');
        })
        ->whereHas('oficinaDestino', function ($query) {
            $query->whereIn('tipo_oficina_id', [1, 2, 3, 7]); // Coincide con tipo_oficina_id en [1, 2, 3]
        })
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);
        
        $weekRange = $this->week ? $this->getWeekRange($this->week) : [
            Carbon::now()->startOfWeek(), // Inicio de la semana actual
            Carbon::now()->endOfWeek()   // Fin de la semana actual
        ];

        $viajes = Viaje::when(auth()->user()->hasRole(['Gerente de Estado']), function ($query) {
                $usuario = auth()->user();
                $estadoId = DB::table('usuario_estados')
                    ->where('id_user', $usuario->id)
                    ->value('id_estado');

                $vehiculoIds = Vehiculo::whereHas('oficina', function ($query) use ($estadoId) {
                    $query->where('estado_id', $estadoId)
                          ->where('tipo_oficina_id', 4);
                })->pluck('vehiculo_id');

                $query->whereIn('vehiculo_id', $vehiculoIds);
            })
            ->when(auth()->user()->hasRole(['SuperAdmin', 'Operaciones Nacionales']), function ($query) {
                $query->whereHas('ruta.oficinaDestino', function ($query) {
                    $query->whereIn('tipo_oficina_id', [1, 2, 3]);
                });
            })
            ->when($this->search, function ($query) {
                $query->where('codigo', 'LIKE', '%' . $this->search . '%');
            })
            ->whereBetween('fecha_salida', $weekRange) // Aplicar el rango de la semana (actual o buscada)
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.viajes.mostrar-viajes-locales', [
            'viajes' => $viajes,
            'rutaas' => $rutaas,
            'vehiculos' => $this->vehiculos,
        ]);
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

    // Obtener oficina del usuario
    $oficinaId = DB::table('users')
        ->where('id', $usuario->id)
        ->value('oficina_id');

    $nombreOficinaUsuario = $oficinaId 
        ? Oficina::where('oficina_id', $oficinaId)->value('nombre')
        : null;

    // Generar el PDF
    $pdf = Pdf::loadView('pdf.viaje_report', [
        'viajes' => $viajes,
        'nombreOficinaUsuario' => $nombreOficinaUsuario,
        'semana' => $this->week 
    ]);
    

    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->stream();
    }, 'reporte_viajes.pdf');
}

public function exportViajesToExcel()
{
    $user = auth()->user();
    $export = new ViajesExport($this->week, $this->search, $user);

    return Excel::download($export, 'viajes_locales.xlsx');
}

}
