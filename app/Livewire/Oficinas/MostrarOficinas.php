<?php

namespace App\Livewire\Oficinas;

use App\Exports\OficinasExport;
use App\Models\Estado;
use App\Models\Sector;
use App\Models\Oficina;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\TipoPago;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\TipoOficina;
use App\Models\CodigoPostal;
use Livewire\WithPagination;
use App\Models\EstatusOficina;
use App\Models\OficinaPersonal;
use App\Models\RoleTipoOficina;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\ServicioOperativo;
use Spatie\Permission\Models\Role;
use App\Models\OficinaSemaforoPostal;
use App\Livewire\Forms\Oficinas\DeleteForm;
use App\Models\MotivoNpcOficina;
use Livewire\WithFileUploads;
use App\Rules\CodigosTelefono;

#[Layout('layouts.app')]
class MostrarOficinas extends Component
{

    use WithPagination, WithFileUploads;

    public DeleteForm $deleteForm;
    public $search = '';
    public $perPage = 50;
    public $sortBy = 'id';
    public $sortDir = 'ASC';
    public $serviciorecibir = 2;
    public $estados = [];
    public $municipios = [];
    public $parroquias = [];
    public $oficinas = [];
    public $codigos_postales = [];
    public $servicios = [];
    public $servicios_operativos = [];
    public $estatus = [];
    public $codigosPostalesSeleccionados = [];
    public $codigosPostalesDisponibles;
    public $tipo_pago = [];
    public $tipos_pagos = [];
    public $showMoreRoles = false;

    public $estadoss, $municipioss, $parroquiass, $tipo_oficina, $oficinas_disponibles, $tipoSeleccionado,
        $cubicacion, $estatus_seleccionado, $condicion_sel, $fecha_inicio, $fecha_fin, $oficina_relacionada, $estado_filtro,
        $oficina_filtro, $municipio_filtro, $numero_punto_cuenta, $oficina_npc, $motivo_npc, $pdf_npc, $modificar_oficina_id;

    public $codigo;
    public $nombre;
    public $correo;
    public $direccion;
    public $tlf;
    public $estado_id;
    public $municipio_id;
    public $parroquia_id;
    public $zona_economica_especial = false;
    public $latitud;
    public $longitud;
    public $tipos = [];
    public $roles = [];
    public $roles_filtrados = [];
    public $rol_asignado = [];
    public $cantidad_por_rol = [];
    public $modalOpen = false;
    public $estatus_id;
    public $municipio_fil = [];
    public $npc = false;
    public $externa = false;
    public $mod = false;

    public function toggleRoles()
    {
        $this->showMoreRoles = !$this->showMore;
    }



    public function openModal()
    {
        $this->modalOpen = true;
    }

    #[On('cerrar-modal-crear')]
    public function closeModal()
    {
        $this->modalOpen = false;
    }

    public function modificar($id)
    {
        $this->modificar_oficina_id = $id;
        $this->mod = true;
    }


    public function mount()
    {
        $this->servicios_operativos = ServicioOperativo::all();
        $this->estados = Estado::where('pais_id', 90)->get();
        $this->codigosPostalesDisponibles = CodigoPostal::all();
        $this->tipos_pagos = TipoPago::all();
        $this->estatus = EstatusOficina::all();
    }

    public function updatedEstadoFiltro($value)
    {
        $this->municipio_filtro = '';

        if (empty($value)) {
            $this->municipio_fil = [];
        } else {
            $this->municipio_fil = Municipio::where('estado_id', $value)->get();
        }
    }

    public function actualizarEstatus($oficina_id, $nuevo_estatus = null)
    {
        if ($nuevo_estatus !== null) {
            $this->estatus_id = $nuevo_estatus;
        }

        if (!$this->estatus_id) {
            return;
        } elseif ($this->estatus_id == 3) {

            $this->npc = true;
            $this->oficina_npc = $oficina_id;
            return;
        } else {
            $oficina = Oficina::find($oficina_id);
            $oficina->estatus_id = $this->estatus_id;
            if (in_array($this->estatus_id, [2, 3])) {
                $oficina->operaciones = false;
            }
            $oficina->save();

            $this->estatus_id = '';
        }
    }

    public function ingresar_npc()
    {
        $this->validate([
            'numero_punto_cuenta' => 'required|string|min:3',
            'motivo_npc' => 'required',
            'pdf_npc' => 'required|file|mimes:pdf|max:10240',
        ]);

        $oficina = Oficina::find($this->oficina_npc);
        $oficina->numero_punto_cuenta = $this->numero_punto_cuenta;
        $oficina->estatus_id = $this->estatus_id;
        $oficina->operaciones = false;
        $oficina->save();

        $path = $this->pdf_npc->store('pdfs_npc', 'public');

        MotivoNpcOficina::create([
            'oficina_id' => $this->oficina_npc,
            'motivo' => $this->motivo_npc,
            'file_path' => $path,
            'nombre_original' => $this->pdf_npc->getClientOriginalName(),
        ]);

        $this->estatus_id = '';
        $this->numero_punto_cuenta = '';
        $this->motivo_npc = '';
        $this->pdf_npc = '';
        $this->dispatch('alertSuccess', message: '¡Numero de Punto de Cuenta registrado exitosamente!');
        $this->cerrar_npc();
    }

    public function cambiar_operacion($oficina_id)
    {
        $oficina = Oficina::find($oficina_id);
        $oficina->operaciones = !$oficina->operaciones;
        $oficina->save();
        $this->dispatch('alertSuccess', message: 'Operaciones actualizadas en la oficina.');
    }

    public function cerrar_npc()
    {
        $this->npc = false;
        $this->estatus_id = '';
        $this->numero_punto_cuenta = '';
        $this->motivo_npc = '';
    }

    public function exportarOficinas()
    {
        /** @var \App\Models\User $usuario */
        $usuario = auth()->user();

        $query = Oficina::where('externa', false);

        if ($usuario->hasRole('Gerente de Estado')) {
            $estadoId = $usuario->oficina->estado_id ?? null;

            $gruposEstados = [
                1  => [1, 2, 24],
                21 => [21, 20],
            ];

            $estadosIds = $estadoId ? ($gruposEstados[$estadoId] ?? [$estadoId]) : null;

            if ($estadosIds) {
                $query->whereIn('estado_id', $estadosIds);
            }
        }

        $query->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('codigo', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('nombre', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('direccion', 'LIKE', '%' . $this->search . '%')
                        ->orWhereHas('tipo_oficina', function ($q2) {
                            $q2->where('nombre', 'LIKE', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->oficina_filtro, function ($q) {
                $q->where('tipo_oficina_id', $this->oficina_filtro);
            })
            ->when($this->estado_filtro, function ($q) {
                $q->where('estado_id', $this->estado_filtro);
            })
            ->when($this->municipio_filtro, function ($q) {
                $q->where('municipio_id', $this->municipio_filtro);
            });

        $oficinas = $query->orderBy('oficina_id', 'asc')->get();

        return Excel::download(new OficinasExport($oficinas), 'oficinas.xlsx');
    }



    public function render()
    {
        $usuario = auth()->user();

        if ($usuario->hasRole('Gerente de Estado')) {
            $estadoId = $usuario->oficina->estado_id ?? null;

            // Agrupaciones: algunos gerentes cubren varios estados
            $gruposEstados = [
                1  => [1, 2, 24], // Distrito Capital también cubre Miranda y La Guaira
                21 => [21, 20],   // Monagas también cubre Delta Amacuro
            ];

            $estadosIds = $estadoId ? ($gruposEstados[$estadoId] ?? [$estadoId]) : null;

            $estados_fil = $estadosIds
                ? Estado::whereIn('estado_id', $estadosIds)->get()
                : Estado::all();

            $AllOficinas = Oficina::with(['tipo_oficina', 'estado', 'municipio', 'parroquia'])
                ->where('externa', false)
                ->when($estadosIds, fn($q) => $q->whereIn('estado_id', $estadosIds))
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('codigo', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('nombre', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('direccion', 'LIKE', '%' . $this->search . '%')
                            ->orWhereHas('tipo_oficina', function ($q2) {
                                $q2->where('nombre', 'LIKE', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->oficina_filtro, function ($query) {
                    $query->where('tipo_oficina_id', $this->oficina_filtro);
                })
                ->when($this->estado_filtro, function ($query) {
                    $query->where('estado_id', $this->estado_filtro);
                })
                ->when($this->municipio_filtro, function ($query) {
                    $query->where('municipio_id', $this->municipio_filtro);
                })
                ->orderBy('nombre', 'asc')
                ->paginate($this->perPage);
        } else {
            $estados_fil = Estado::all();
            // No hay filtro de estado si el usuario no es "Gerente de Estado"
            $AllOficinas = Oficina::with(['tipo_oficina', 'estado', 'municipio', 'parroquia'])
                ->where('externa', false)
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('codigo', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('nombre', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('direccion', 'LIKE', '%' . $this->search . '%')
                            ->orWhereHas('tipo_oficina', function ($q2) {
                                $q2->where('nombre', 'LIKE', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->oficina_filtro, function ($query) {
                    $query->where('tipo_oficina_id', $this->oficina_filtro);
                })
                ->when($this->estado_filtro, function ($query) {
                    $query->where('estado_id', $this->estado_filtro);
                })
                ->when($this->municipio_filtro, function ($query) {
                    $query->where('municipio_id', $this->municipio_filtro);
                })
                ->orderBy('oficina_id', 'asc')
                ->paginate($this->perPage);
        }


        $tipos_oficina = TipoOficina::whereNotIn('tipo_oficina_id', [5, 6, 7])->get(); // Obtener todos los tipos de oficina
        $condiciones = OficinaSemaforoPostal::getCondiciones();
        return view('livewire.oficinas.mostrar-oficinas',  compact('condiciones', 'AllOficinas', 'tipos_oficina', 'estados_fil'));
    }
}
