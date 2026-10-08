<?php

namespace App\Livewire\SemaforoPostal;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Region;
use App\Models\Oficina;
use App\Models\Vehiculo;

#[Layout('layouts.app')]
class SemaforoPostal extends Component
{
    public $region = null;
    public $regiones = [];
    public $ver_detalle = false; 
    public $estado_id = null;

    public function mount()
    {
        $this->regiones = Region::all();
    }

    // Método para seleccionar un estado desde la vista
    public function seleccionar_estado($estado_id)
    {
        $this->estado_id = $estado_id;
    }

    public function mostrar_detalles($region_id = null)
    {
        if ($region_id) {
            $this->region = $region_id;
        }
        $this->ver_detalle = true;
    }

    public function volver_al_resumen()
    {
        $this->ver_detalle = false;
        $this->estado_id = null; // Resetea la selección de estado
    }

    public function render()
    {
        // 1. Consulta base común de todas las oficinas (tipos 1,2,3 para datos de semaforo)
        $query = Oficina::query()
            ->whereIn('tipo_oficina_id', [1, 2, 3])
            ->when($this->region, function ($q) {
                $q->whereHas('estado.region', function ($q2) {
                    $q2->where('region_id', $this->region);
                });
            })
            ->with(['estado.region', 'semaforo_postal']);

        $oficinas = $query->get();

        // Todos los vehículos agrupados por estado_id (sin restricción de tipo de oficina)
        $vehiculos_por_estado = Vehiculo::query()
            ->whereNotNull('oficina_id')
            ->with('oficina:oficina_id,estado_id')
            ->get()
            ->groupBy(fn($v) => $v->oficina?->estado_id);

        // 2. Lógica de Resumen General por Región
        $resumen_general = $oficinas->groupBy(fn($oficina) => $oficina->estado->region->nombre)
            ->map(function ($oficinas_region, $region_nombre) use ($vehiculos_por_estado) {
                $estado_ids = $oficinas_region->pluck('estado_id')->unique()->values()->all();
                $vehiculos_region = $vehiculos_por_estado->filter(fn($_, $k) => \in_array($k, $estado_ids))->flatten();

                return [
                    'region' => $region_nombre,
                    'total_oficinas' => $oficinas_region->count(),
                    'vehiculos_activo' => $vehiculos_region->where('Activo', true)->count(),
                    'vehiculos_inoperativos' => $vehiculos_region->where('Activo', false)->count(),
                    'operativas' => $oficinas_region->where('operaciones', true)->count(),
                    'inoperativas' => $oficinas_region->where('operaciones', false)->count(),
                    'comodato' => $oficinas_region->filter(fn($oficina) => $oficina->semaforo_postal?->condicion === 'En Comodato')->count(),
                    'arrendadas' => $oficinas_region->filter(fn($oficina) => $oficina->semaforo_postal?->condicion === 'Arrendada')->count(),
                    'propias' => $oficinas_region->filter(fn($oficina) => $oficina->semaforo_postal?->condicion === 'Propia Ipostel')->count(),
                ];
            });

        // 3. Lógica de Detalles por Estado dentro de cada región
        $detalle_por_estado = $oficinas->groupBy(fn($o) => $o->estado->region->nombre)
            ->map(function ($oficinas_region, $region_nombre) use ($vehiculos_por_estado) {
                return [
                    'region' => $region_nombre,
                    'estados' => $oficinas_region->groupBy(fn($o) => $o->estado->nombre)
                        ->map(function ($oficinas_estado, $estado_nombre) use ($vehiculos_por_estado) {
                            $stats = [
                                'Propia Ipostel' => ['operativas' => 0, 'inoperativas' => 0],
                                'Arrendada'      => ['operativas' => 0, 'inoperativas' => 0],
                                'En Comodato'    => ['operativas' => 0, 'inoperativas' => 0],
                            ];

                            foreach ($oficinas_estado as $oficina) {
                                $cond = $oficina->semaforo_postal?->condicion;
                                $op = (bool) $oficina->operaciones;
                                if (isset($stats[$cond])) {
                                    $op ? $stats[$cond]['operativas']++ : $stats[$cond]['inoperativas']++;
                                }
                            }

                            $estado_id = $oficinas_estado->first()->estado->estado_id;
                            $vehiculos_estado = $vehiculos_por_estado->get($estado_id, collect());

                            $total_operativas = $oficinas_estado->where('operaciones', true)->count();
                            $total_inoperativas = $oficinas_estado->where('operaciones', false)->count();

                            return [
                                'estado' => $estado_nombre,
                                'estado_id' => $estado_id,
                                'vehiculos_activo' => $vehiculos_estado->where('Activo', true)->count(),
                                'vehiculos_inoperativos' => $vehiculos_estado->where('Activo', false)->count(),
                                'propias' => [
                                    'operativas' => $stats['Propia Ipostel']['operativas'],
                                    'inoperativas' => $stats['Propia Ipostel']['inoperativas'],
                                ],
                                'arrendadas' => [
                                    'operativas' => $stats['Arrendada']['operativas'],
                                    'inoperativas' => $stats['Arrendada']['inoperativas'],
                                ],
                                'comodato' => [
                                    'operativas' => $stats['En Comodato']['operativas'],
                                    'inoperativas' => $stats['En Comodato']['inoperativas'],
                                ],
                                'total_operativas' => $total_operativas,
                                'total_inoperativas' => $total_inoperativas,
                            ];
                        }),
                ];
            });

        // 4. Lógica de oficinas filtradas por estado seleccionado
        $detalles_oficinas = collect();
        if ($this->estado_id) {
            $detalles_oficinas = Oficina::query()
                ->where('estado_id', $this->estado_id)
                ->whereIn('tipo_oficina_id', [1, 2, 3])
                ->with(['estado', 'semaforo_postal', 'vehiculos'])
                ->get()
                ->map(function ($oficina) {
                    return [
                        'oficina_id' => $oficina->oficina_id,
                        'oficina' => $oficina->nombre,
                        'estado_id' => $oficina->estado->estado_id,
                        'estado' => $oficina->estado->nombre,
                        'operativa' => (bool) $oficina->operaciones,
                        'estatus_legal' => $oficina->semaforo_postal?->condicion,
                        'tiene_vehiculos' => $oficina->vehiculos->count() > 0,
                        'vehiculos' => [
                            'operativos' => $oficina->vehiculos->where('Activo', true)->count(),
                            'inoperativos' => $oficina->vehiculos->where('Activo', false)->count(),
                        ],
                    ];
                });
        }

        return view('livewire.semaforo-postal.semaforo-postal', [
            'grupos' => $resumen_general,
            'detalles' => $detalle_por_estado,
            'oficinas_det' => $detalles_oficinas,
        ]);
    }
}
