<?php

namespace App\Livewire\Envios;

use Livewire\WithPagination;
use App\Models\Servicio;
use App\Models\Envio;
use App\Models\User;
use App\Models\Oficina;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class ListadoVentasInternacionales extends Component
{

    use WithPagination;

    public $perPage = 5;
    public $sortBy = 'servicio_id';
    public $sortDir = 'ASC';
    public $search = '';

    public $oficina_id;
    public $usuarios = [];
    public $usuario_id;

    public $totalCosto = 0;
    public $totalEnvios = 0;

    public $desde, $hasta, $envios, $montos;

    public function updatedOficinaId($value)
    {

        if ($value) {
            $this->usuarios = User::where('oficina_id', $value)->get();
            $this->usuario_id = null;
        }
        else {
            $this->usuarios = [];
            $this->usuario_id = null;
        }

    }

    public function consultarEnvios($servicio_id)
    {
        $this->dispatch('consultaEnvios', [
        'oficina_id' => $this->oficina_id,
        'servicio_id' => $servicio_id,
        ]);
    }

    public function render()
    {
        $oficinas = Oficina::where('oficina_relacionada_id', auth()->user()->oficina_id)->get();   

        $usuario_id = $this->usuario_id;
        $oficina_id = $this->oficina_id;

        if (auth()->user()->hasRole('Jefe de OPT')) {
            if(!$this->usuarios)
            {
                $this->usuarios = User::where('oficina_id', auth()->user()->oficina_id)->get();
            }
            if(!$this->oficina_id)
            {
                $this->oficina_id = Oficina::find(auth()->user()->oficina_id)->oficina_id;
            }
        }

        $fechaInicio = $this->desde;
        $fechaFin = $this->hasta;

        if ((!$fechaInicio)||(!$fechaFin)) {
            $fechaInicio = now()->startOfDay();
            $fechaFin = now()->endOfDay();

            $this->desde = now()->format('Y-m-d');
            $this->hasta = now()->format('Y-m-d');
        }
        else {
            $fechaInicio = $fechaInicio."  00:00:00";
            $fechaFin = $fechaFin."  23:59:59";
        }

        $todos_servicios = Servicio::where('nacional', false)->get();
        $servicios = Servicio::where('nacional', false)->paginate($this->perPage);

        foreach ($todos_servicios as $servicio) {
         
            $envios = Envio::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('servicio_id', $servicio->servicio_id);

            if ($oficina_id) {

                $envios = $envios->where('oficina_id', $oficina_id);

                if ($usuario_id) {

                    $envios = $envios->where('usuario_id', $usuario_id);

                }

            }
            else {

                $envios = $envios->where('oficina_id', auth()->user()->oficina_id)
                ->where('usuario_id', auth()->user()->id);

            }

            $envios = $envios->count();

            $montos = Envio::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('servicio_id', $servicio->servicio_id);

            if ($oficina_id) {

                $montos = $montos->where('oficina_id', $oficina_id);

                if ($usuario_id) {

                    $montos = $montos->where('usuario_id', $usuario_id);

                }

            }
            else {

                $montos = $montos->where('oficina_id', auth()->user()->oficina_id)
                ->where('usuario_id', auth()->user()->id);

            }

            $montos = $montos->sum('coste');

            $this->envios[$servicio->servicio_id] = $envios;

            $this->montos[$servicio->servicio_id] = $montos;

        }

        $this->totalEnvios = array_sum($this->envios);
        $this->totalCosto = array_sum($this->montos);

        return view('livewire.envios.listado-ventas-internacionales', compact('todos_servicios', 'oficinas'));
    }
}
