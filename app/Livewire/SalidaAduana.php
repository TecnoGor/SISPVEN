<?php

namespace App\Livewire;

use App\Models\Envio;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Carbon;

#[Layout('layouts.app')]
class SalidaAduana extends Component
{
    public $busqueda = '';
    public $enviosSeleccionados;

    public function mount()
    {
        $this->enviosSeleccionados = collect();
    }

    public function getEnviosDisponiblesProperty()
    {
        $usuario = Auth::user();
        $oficinaId = $usuario->oficina_id;

        return Envio::whereHas('almacenAduana', function ($query) use ($oficinaId) {
                $query->where('oficina_id', $oficinaId)
                      ->where('estatus', true);
            })
            ->where('codigo_envio', 'like', '%' . $this->busqueda . '%')
            ->with('almacenAduana')
            ->get()
            ->reject(fn($envio) => $this->enviosSeleccionados->contains('envio_id', $envio->envio_id));
    }

    public function seleccionarEnvio($envioId)
    {
        $envio = Envio::with('almacenAduana')->find($envioId);

        if ($envio && !$this->enviosSeleccionados->contains('envio_id', $envio->envio_id)) {
            $this->enviosSeleccionados->push($envio);
        }
    }

    public function removerEnvio($envioId)
    {
        $this->enviosSeleccionados = $this->enviosSeleccionados->reject(
            fn($envio) => $envio->envio_id == $envioId
        );
    }

    public function procesarSalida()
    {
        $usuario = auth()->user();
        $fechaSalida = Carbon::now();

        foreach ($this->enviosSeleccionados as $envio) {
            // 1. Actualizar almacen_aduana
            \DB::table('almacen_aduana')
                ->where('envio_id', $envio->envio_id)
                ->update([
                    'estatus' => false,
                    'Salida' => $fechaSalida,
                ]);

            // 2. Actualizar envios_almacen
            \DB::table('envios_almacen')
                ->where('envio_id', $envio->envio_id)
                ->where('oficina_id', $usuario->oficina_id)
                ->update([
                    'estatus' => true,
                ]);

            // 3. Registrar en envios_encaminamiento
            \DB::table('envios_encaminamiento')->insert([
                'envio_id' => $envio->envio_id,
                'oficina_id' => $usuario->oficina_id,
                'usuario_id' => $usuario->id,
                'estatus_id' => 10,
                'devolucion' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se registró salida de aduana de " . $this->enviosSeleccionados->count() . " envío(s)",
        ]);

        $this->enviosSeleccionados = collect();
    $this->dispatch('alertSuccess', message: 'Salida Registrada exitosamente!');
    }

    public function render()
    {
        return view('livewire.salida-aduana');
    }
}
