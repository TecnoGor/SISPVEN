<?php

namespace App\Livewire\Forms\Oficinas;

use App\Models\Oficina;
use App\Models\Saca;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;
use Livewire\Form;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Createform extends Form
{
    public $open = false;
    public $tipoSeleccionado;
    public $oficinaSeleccionada;

    public function create()
    {
        $this->open = true;
    }
public function store()
{
    $usuario = auth()->user();
    $oficinaId = $usuario->oficina_id;

    try {
        DB::transaction(function () use ($usuario, $oficinaId) {
            // 1. Resolver el numero_despacho_id del par (origen, destino).
            //    El correlativo del despacho es por par origen->destino: todas las valijas
            //    creadas para ese par comparten el mismo despacho ABIERTO hasta que se le
            //    da salida. Lógica compartida en DespachoService (misma que usa la edición
            //    de destino de una valija en DetallesSacas).
            $numeroDespachoId = app(\App\Services\DespachoService::class)
                ->resolverDespachoActivo($oficinaId, (int) $this->oficinaSeleccionada);

            // 2. Generar código único de saca
            $oficinacodigo = substr(\App\Models\Oficina::where('oficina_id', $this->oficinaSeleccionada)->value('codigo'), 0, 9);
            do {
                $aleatorio = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));
                $codigo = $oficinacodigo . '-' . $aleatorio . '-' . date('Y');
            } while (\App\Models\Saca::where('codigo_saca', $codigo)->exists());

            // 3. Crear saca
            $saca = \App\Models\Saca::create([
                'tipo_saca_id'       => $this->tipoSeleccionado,
                'oficina_id'         => $oficinaId,
                'usuario_id'         => $usuario->id,
                'oficina_destino_id' => $this->oficinaSeleccionada,
                'codigo_saca'        => $codigo,
                'cerrado'            => false,
                'numero_despacho_id' => $numeroDespachoId,
            ]);

            // 3.1 Registrar el encaminamiento inicial (Saca Creada) en su oficina origen.
            \App\Models\SacaEncaminamiento::create([
                'saca_id'         => $saca->saca_id,
                'oficina_id'      => $oficinaId,
                'saca_estatus_id' => \App\Models\Saca::ESTATUS_CREADA,
                'usuario_id'      => $usuario->id,
            ]);

            \App\Models\UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'create',
                'descripcion' => "usuario $usuario->id hizo un create de una valija con id: $saca->saca_id"
            ]);
        });

        $this->reset();
        $this->open = false;
    } catch (\Throwable $e) {
        Log::error('Error al crear saca', [
            'oficina_id' => $oficinaId,
            'usuario_id' => $usuario->id,
            'error'      => $e->getMessage(),
        ]);
        throw $e;
    }
}
}
