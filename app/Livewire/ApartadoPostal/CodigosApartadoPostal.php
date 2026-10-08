<?php

namespace App\Livewire\ApartadoPostal;

use App\Models\Oficina;

use Carbon\Carbon;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApartadosExport;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\RegistroApartado;
use App\Models\CodigoApartadoPostal;
use App\Models\UsuarioSeguimiento;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class CodigosApartadoPostal extends Component
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
    public $perPage = 10;
    public $desde;
    public $hasta;
    public $apartadosex = [];

    public function openModal()
    {
        $this->modalOpen = true;
    }

    public function closeModal()
    {
        $this->modalOpen = false;
    }



    public function mount()
    {
        $this->usuario = auth()->user();
        $oficina = $this->usuario->oficina_id;
        $this->codigo_ubicacion = Oficina::where('oficina_id', $oficina)->pluck('codigo_ubicacion')->first();

        $this->apartados_postales = CodigoApartadoPostal::where('apartado', 'LIKE', $this->codigo_ubicacion . '%')
            ->where('oficina_id', $this->usuario->oficina_id)
            ->orderBy('codigo_apartado_id', 'asc')->get();

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

    public function inoperativo(CodigoApartadoPostal $apartado)
    {
        $apartado->operativo = false;
        $apartado->save();
        $this->desactivar($apartado);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se marcó como inoperativo el código de apartado postal ({$apartado->codigo_apartado_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Apartado Inhabilitado exitosamente!');
    }

    public function operativo(CodigoApartadoPostal $apartado)
    {
        $apartado->operativo = true;
        $apartado->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se marcó como operativo el código de apartado postal ({$apartado->codigo_apartado_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Apartado Habilitado exitosamente!');
    }

    public function desactivar(CodigoApartadoPostal $apartado)
    {
        $apartado->activo = false;
        $apartado->save();
        $registro_apartado = RegistroApartado::where('activo', true)
            ->where('codigo_apartado_id', $apartado->codigo_apartado_id)->latest()->first();
        if ($registro_apartado) {
            $registro_apartado->activo = false;
            $registro_apartado->save();
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se desactivó el código de apartado postal ({$apartado->codigo_apartado_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Apartado Desactivado exitosamente!');
    }

    public function activar(CodigoApartadoPostal $apartado)
    {
        $apartado->activo = true;
        $apartado->save();
        $registro_apartado = RegistroApartado::where('activo', false)
            ->where('codigo_apartado_id', $apartado->codigo_apartado_id)->latest()->first();
        if ($registro_apartado) {
            $registro_apartado->activo = true;
            $registro_apartado->save();
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se activó el código de apartado postal ({$apartado->codigo_apartado_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Apartado Activado exitosamente!');
    }


    public function generarApartados()
    {
        $this->validate(['cantidad' => 'required|integer|min:1|max:100']);

        $codigos_existentes = CodigoApartadoPostal::where('oficina_id', $this->usuario->oficina_id)
            ->pluck('apartado');

        if ($codigos_existentes->isNotEmpty()) {
            // Si existen códigos previos, obtenemos el último código
            $ultimo_numero = $codigos_existentes->last();
        } else {
            $ultimo_numero = 0;
        }

        // Generamos los nuevos códigos
        $nuevos_codigos = [];
        for ($i = 1; $i <= $this->cantidad; $i++) {
            $nuevo_codigo = $ultimo_numero + $i;

            // Verificamos si el código ya existe
            $codigo_existente = CodigoApartadoPostal::where('apartado', $nuevo_codigo)->where('oficina_id', $this->usuario->oficina_id)->exists();

            // Si no existe, lo agregamos a la lista de nuevos códigos
            if (!$codigo_existente) {
                $nuevos_codigos[] = $nuevo_codigo;
            } else {
                // Si ya existe, intentamos con el siguiente número (sin perder el índice)
                $i--; // Decrementamos para intentar generar el mismo número otra vez
            }
        }

        $this->submit($nuevos_codigos);
    }


    public function submit($nuevos_codigos)
    {
        foreach ($nuevos_codigos as $codigo) {
            CodigoApartadoPostal::create([
                'apartado' => $codigo,
                'oficina_id' => $this->usuario->oficina_id,
                'operativo' => true,
                'activo' => false,
            ]);
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se generaron " . count($nuevos_codigos) . " código(s) de apartado postal en la oficina ({$this->usuario->oficina_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Apartados Creados exitosamente!');
        $this->modalOpen = false;
    }

    public function updatedPerPage()
    {
        $this->resetPage(); // Restablece la página actual a la primera
    }

    public function reporte_excel()
    {
        $apartados = CodigoApartadoPostal::where('oficina_id', $this->usuario->oficina_id)
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

        return Excel::download(new ApartadosExport($apartados), 'apartados.xlsx');
    }

    public function exportarPdf()
    {
        $usuario = auth()->user();

        if (!$usuario) {
            $this->dispatch('alertError', message: 'Usuario no autenticado');
            return;
        }

        // Obtén la oficina del usuario
        $oficina = Oficina::find($usuario->oficina_id);

        if (!$oficina) {
            $this->dispatch('alertError', message: 'Oficina no encontrada');
            return;
        }

        $apartados = CodigoApartadoPostal::where('oficina_id', $usuario->oficina_id)
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

        // Agrega los días restantes a cada apartado
        foreach ($apartados as $ap) {
            $registro = RegistroApartado::where('codigo_apartado_id', $ap->codigo_apartado_id)
                ->where('activo', true)
                ->latest()
                ->first();

            if ($registro) {
                $dias_transcurridos = Carbon::parse($registro->created_at)->diffInDays(Carbon::now());
                $dias_del_año = Carbon::now()->isLeapYear() ? 366 : 365;
                $ap->dias_restantes = $dias_del_año - $dias_transcurridos;
            } else {
                $ap->dias_restantes = '--';
            }
        }

        $pdf = Pdf::loadView('pdf.reporte-apartados', [
            'apartados' => $apartados,
            'oficinaNombre' => $oficina->nombre,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte-apartados-' . now()->format('Ymd_His') . '.pdf');
    }


    public function render()
    {
        $apartadosp = CodigoApartadoPostal::where('oficina_id', $this->usuario->oficina_id)
            ->when($this->search, function ($query) {
                $query->where('apartado', 'LIKE', '%' . $this->search . '%')

                    ->orWhereHas('registro_apartado', function ($query) {
                        $query->where('documento', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('nombre', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('apellido', 'LIKE', '%' . $this->search . '%');
                    });
            })

            // Filtrar por fecha 
            ->when($this->desde, function ($query) {
                $query->whereDate('created_at', '>=', $this->desde);
            })
            ->when($this->hasta, function ($query) {
                $query->whereDate('created_at', '<=', $this->hasta);
            })

            ->orderBy('codigo_apartado_id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.apartado-postal.codigos-apartado-postal', compact('apartadosp'));
    }
}
