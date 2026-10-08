<?php

namespace App\Livewire;

use App\Models\Envio;
use App\Models\AlmacenAduana;
use App\Models\EnvioEncaminamiento;
use App\Models\EnvioAlmacen; // Asegúrate que el modelo está bien nombrado
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class EntradaAduana extends Component
{
    public $codigoEnvioBusqueda = '';
    public $enviosSeleccionados = [];
    public $enviosIds = [];
    public $mensajeBusqueda = '';
    public $codigoValido = false;

    public function updatedCodigoEnvioBusqueda()
    {
        $this->validarCodigo();
    }

    public function validarCodigo()
    {
        $codigo = trim($this->codigoEnvioBusqueda);
        $this->codigoValido = false;
        $this->mensajeBusqueda = '';

        if ($codigo === '') return;

        $envio = Envio::where('codigo_envio', $codigo)
                      ->where('tipo_envio', 'internacional')
                      ->first();

        if (!$envio) {
            $this->mensajeBusqueda = 'Código inválido o no es un envío internacional';
            return;
        }

        if (in_array($envio->envio_id, $this->enviosIds)) {
            $this->mensajeBusqueda = 'Este envío ya fue agregado';
            return;
        }

        $this->mensajeBusqueda = 'Envío conseguido';
        $this->codigoValido = true;
    }

    public function agregarEnvios()
    {
        if (!$this->codigoValido) return;

        $envio = Envio::where('codigo_envio', $this->codigoEnvioBusqueda)
                      ->where('tipo_envio', 'internacional')
                      ->first();

        if ($envio && !in_array($envio->envio_id, $this->enviosIds)) {
            $this->enviosSeleccionados[] = $envio;
            $this->enviosIds[] = $envio->envio_id;
        }

        $this->codigoEnvioBusqueda = '';
        $this->mensajeBusqueda = '';
        $this->codigoValido = false;
    }

    public function eliminarEnvio($envio_id)
    {
        $this->enviosSeleccionados = array_filter($this->enviosSeleccionados, fn($envio) => $envio->envio_id !== $envio_id);
        $this->enviosIds = array_filter($this->enviosIds, fn($id) => $id !== $envio_id);
    }

    public function create()
    {
        $usuario = Auth::user();

        foreach ($this->enviosSeleccionados as $envio) {
            // ✅ Buscar y actualizar en envios_almacen
            $registroEnviosAlmacen = EnvioAlmacen::where('envio_id', $envio->envio_id)
                                                  ->where('estatus', true)
                                                  ->first();

            if ($registroEnviosAlmacen) {
                $registroEnviosAlmacen->update(['estatus' => false]);
                $registroEnviosAlmacen->update(['Salida' => now()]);
            }

            // ✅ AlmacenAduana
            $almacen = AlmacenAduana::where('envio_id', $envio->envio_id)->first();

            if ($almacen) {
                if ($almacen->estatus === true) {
                    $almacen->update(['estatus' => false]);
                }
            } else {
                AlmacenAduana::create([
                    'envio_id' => $envio->envio_id,
                    'oficina_id' => $usuario->oficina_id,
                    'codigo' => $envio->codigo_envio,
                    'estatus' => true,
                    'Entrada' => now(),
                ]);
            }

            // ✅ Registro en envios_encaminamiento
            EnvioEncaminamiento::create([
                'envio_id'           => $envio->envio_id,
                'oficina_id'         => $usuario->oficina_id,
                'oficina_externa_id' => null,
                'usuario_id'         => $usuario->id,
                'viaje_id'           => $this->viaje_id ?? null,
                'estatus_id'         => 7,
                'devolucion'         => false,
            ]);
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se registró entrada a aduana de " . count($this->enviosSeleccionados) . " envío(s)",
        ]);

        // ✅ Resetear estado
        $this->enviosSeleccionados = [];
        $this->enviosIds = [];
        $this->codigoEnvioBusqueda = '';
        $this->mensajeBusqueda = '';
        $this->codigoValido = false;

        $this->dispatch('alertSuccess', ['message' => 'Envíos registrados exitosamente.']);
    }

    public function render()
    {
        return view('livewire.entrada-aduana', [
            'envios' => $this->enviosSeleccionados,
            'mensajeBusqueda' => $this->mensajeBusqueda,
            'codigoValido' => $this->codigoValido
        ]);
    }
}
