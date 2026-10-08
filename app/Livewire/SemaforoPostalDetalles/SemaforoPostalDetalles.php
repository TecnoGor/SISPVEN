<?php

namespace App\Livewire\SemaforoPostalDetalles;

use Livewire\Attributes\Layout;
use App\Models\Region;
use App\Models\Oficina;
use Livewire\Component;

#[Layout('layouts.app')]
class SemaforoPostalDetalles extends Component
{
    public $region = null;
    public $regiones = [];


    public function mount()
    {
        $this->regiones = Region::all();
    }

    public function render()
    {
        $oficinas = Oficina::query()->whereIn('tipo_oficina_id', [1,2,3])
            ->when($this->region, function ($q) {
                $q->whereHas('estado.region', function ($q2) {
                    $q2->where('region_id', $this->region);
                });
            })->with(['estado.region', 'semaforo_postal'])->get();

            $grupos = $oficinas ->groupBy(fn($o) => $o->estado->region->nombre)->map(function ($oficinas_region, $region_nombre) { 
                return [ 
                    'region' => $region_nombre, 
                    'estados' => $oficinas_region 
                        ->groupBy(fn($o) => $o->estado->nombre)
                        ->map(function ($oficinas_estado, $estado_nombre) {
                            
                            $stats = [ 
                                'Propia Ipostel' => ['operativas' => 0, 'inoperativas' => 0], 
                                'Arrendada' => ['operativas' => 0, 'inoperativas' => 0], 
                                'En Comodato' => ['operativas' => 0, 'inoperativas' => 0], 
                            ]; 

                        foreach ($oficinas_estado as $oficina) { 
                            $cond = $oficina->semaforo_postal?->condicion; 
                            $op = (bool) $oficina->operaciones; 
                                if (isset($stats[$cond])) { 
                                    $op ? $stats[$cond]['operativas']++ : $stats[$cond]['inoperativas']++; 
                                } 
                            } 
                            $total_operativas = $oficinas_estado->where('operaciones', true)->count(); 
                            $total_inoperativas = $oficinas_estado->where('operaciones', false)->count(); 

                            return [ 'estado' => $estado_nombre, 
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
                                //total del estado
                                'total_operativas' => $total_operativas, 
                                'total_inoperativas' => $total_inoperativas, ]; 
                            }), 
                        ]; 
                    });


        return view('livewire.semaforo-postal-detalles.semaforo-postal-detalles');
    }
}
