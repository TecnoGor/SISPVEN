<?php

namespace App\Livewire\Forms\Rutas;

use Livewire\Form;
use App\Models\Estado;
use App\Models\Oficina;
use App\Models\RutasModelo;
use App\Models\PlataformaRuta;
use App\Models\UsuarioSeguimiento;
use App\Models\RutaPuntoEntrega; // Importar el modelo
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateForm extends Form
{
    public $open = false;
    public $nombre;
    public $origen;
    public $destino;
    public $distancia;
    public $tiempo;
    public $puntosEntrega = [];

    public function create()
    {
        $this->open = true;
    }
 
    public function store(): bool
    {
        try {
            DB::transaction(function () {
                // Guardar el registro principal en RutasModelo
                $ruta = RutasModelo::create([
                    'distancia' => $this->distancia,
                    'ruta' => $this->nombre,
                    'oficina_id_origen' => $this->origen,
                    'oficina_id_destino' => $this->destino,
                    'tiempo' => $this->tiempo,
                    'activo' => true,
                ]);

                // Guardar los puntos de entrega en rutas_puntos_entregas
                foreach ($this->puntosEntrega as $oficinaId) {
                    RutaPuntoEntrega::create([
                        'ruta_id' => $ruta->ruta_id, // ID de la ruta recién creada
                        'oficina_id' => $oficinaId, // ID de la oficina de puntosEntrega
                    ]);
                }

                // Guardar el seguimiento del usuario
                $usuario = auth()->user();
                UsuarioSeguimiento::create([
                    'usuario_id' => $usuario->id,
                    'accion' => 'create',
                    'descripcion' => "Usuario {$usuario->id} creó una ruta.",
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Error al crear ruta', [
                'error' => $e->getMessage(),
                'origen' => $this->origen,
                'destino' => $this->destino,
            ]);
            return false;
        }

        // Reiniciar los campos y cerrar el formulario
        $this->reset();
        $this->open = false;

        return true;
    }
}
