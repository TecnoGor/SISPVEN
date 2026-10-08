<?php

namespace App\Livewire\Recolectas;

use App\Models\Recolecta;
use App\Models\RecolectaEstatus;
use App\Models\UsuarioSeguimiento;
use App\Services\Recolectas\ProcesarRecolectaService;
use App\Services\Recolectas\RecolectaFlowService;
use App\Services\Recolectas\TransicionRecolectaInvalidaException;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Pantalla de la oficina recolectora: solicitudes de recolecta creadas
 * desde la app de clientes para esta oficina. Acciones:
 *  - Confirmar pago (hook manual mientras la verificación automática está
 *    en stand-by): pago_reportado → pago_confirmado.
 *  - Procesar (paquete recibido): pago_confirmado → recolectada, creando
 *    el envío Iposplus real con encaminamiento y facturación.
 *  - Rechazar con motivo.
 */
#[Layout('layouts.app')]
class RecolectasOficina extends Component
{
    use WithPagination;

    public $usuario = [];
    public $search;
    public $desde;
    public $hasta;
    public $filtro_estatus = '';
    public $por_pagina = 10;

    // Modal de rechazo
    public $modal_rechazo = false;
    public $recolecta_rechazo_id = null;
    public $motivo_rechazo = '';

    public function mount()
    {
        $this->usuario = auth()->user();
    }

    public function confirmarPago($recolectaId, RecolectaFlowService $flow)
    {
        $recolecta = $this->recolectaDeOficina($recolectaId);
        if (!$recolecta) {
            return;
        }

        if (!$recolecta->tieneEstatus(RecolectaEstatus::PAGO_REPORTADO)) {
            $this->dispatch('alertSuccess2', message: 'La recolecta no tiene un pago reportado por confirmar.');
            return;
        }

        try {
            $flow->confirmarPago($recolecta, auth()->user());
        } catch (TransicionRecolectaInvalidaException $e) {
            $this->dispatch('alertSuccess2', message: $e->getMessage());
            return;
        }

        UsuarioSeguimiento::create([
            'usuario_id' => auth()->user()->id,
            'accion' => 'update',
            'descripcion' => "Se confirmó el pago de la recolecta ({$recolecta->codigo})",
        ]);

        $this->dispatch('alertSuccess', message: 'Pago confirmado. La recolecta puede recogerse.');
    }

    public function procesar($recolectaId, ProcesarRecolectaService $procesador)
    {
        $recolecta = $this->recolectaDeOficina($recolectaId);
        if (!$recolecta) {
            return;
        }

        try {
            $envio = $procesador->procesar($recolecta, auth()->user());
        } catch (TransicionRecolectaInvalidaException $e) {
            $this->dispatch('alertSuccess2', message: $e->getMessage());
            return;
        } catch (\Throwable $e) {
            Log::error('Error al procesar recolecta', [
                'recolecta_id' => $recolectaId,
                'usuario_id' => auth()->user()->id,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('alertSuccess2', message: 'Ocurrió un error al procesar la recolecta. Intente de nuevo.');
            return;
        }

        $this->marcarNotificacionLeida($recolecta->recolecta_id);
        $this->dispatch('notificacion-leida');
        $this->dispatch('alertSuccess', message: "Recolecta procesada. Envío creado: {$envio->codigo_envio}");
    }

    public function abrirRechazo($recolectaId)
    {
        $this->recolecta_rechazo_id = $recolectaId;
        $this->motivo_rechazo = '';
        $this->modal_rechazo = true;
    }

    public function cerrarRechazo()
    {
        $this->modal_rechazo = false;
        $this->recolecta_rechazo_id = null;
        $this->motivo_rechazo = '';
    }

    public function rechazar(RecolectaFlowService $flow)
    {
        $this->validate(
            ['motivo_rechazo' => 'required|max:255'],
            ['motivo_rechazo.required' => 'Debe indicar el motivo del rechazo']
        );

        $recolecta = $this->recolectaDeOficina($this->recolecta_rechazo_id);
        if (!$recolecta) {
            return;
        }

        try {
            $flow->transicionar($recolecta, RecolectaEstatus::RECHAZADA);
        } catch (TransicionRecolectaInvalidaException $e) {
            $this->dispatch('alertSuccess2', message: $e->getMessage());
            return;
        }

        $recolecta->motivo_rechazo = $this->motivo_rechazo;
        $recolecta->save();

        UsuarioSeguimiento::create([
            'usuario_id' => auth()->user()->id,
            'accion' => 'update',
            'descripcion' => "Se rechazó la recolecta ({$recolecta->codigo}): {$this->motivo_rechazo}",
        ]);

        $this->marcarNotificacionLeida($recolecta->recolecta_id);
        $this->dispatch('notificacion-leida');
        $this->cerrarRechazo();
        $this->dispatch('alertSuccess', message: 'Recolecta rechazada.');
    }

    private function recolectaDeOficina($recolectaId): ?Recolecta
    {
        $recolecta = Recolecta::deOficina($this->usuario['oficina_id'])
            ->with('estatus', 'pagos.tipoPago')
            ->where('recolecta_id', $recolectaId)
            ->first();

        if (!$recolecta) {
            $this->dispatch('alertSuccess2', message: 'Recolecta no encontrada para esta oficina.');
        }

        return $recolecta;
    }

    /**
     * Marca como leída la notificación de tipo recolecta asociada. El
     * filtrado del payload se hace en PHP porque la columna `data` es
     * `text` en PostgreSQL (mismo patrón que ConfirmacionTelegrama).
     */
    private function marcarNotificacionLeida($recolectaId): void
    {
        DatabaseNotification::whereNull('read_at')
            ->get()
            ->filter(function ($notif) use ($recolectaId) {
                return ($notif->data['tipo'] ?? null) === 'recolecta'
                    && (int) ($notif->data['recolecta_id'] ?? 0) === (int) $recolectaId;
            })
            ->each->markAsRead();
    }

    public function render()
    {
        $recolectas = Recolecta::deOficina($this->usuario['oficina_id'])
            ->with('estatus', 'usuarioApp', 'pagos.tipoPago', 'envio')
            ->when($this->filtro_estatus, function ($q) {
                $q->whereHas('estatus', fn ($sub) => $sub->where('slug', $this->filtro_estatus));
            })
            ->when($this->desde, fn ($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn ($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('codigo', 'like', '%' . $this->search . '%')
                        ->orWhere('documento_rem', 'like', '%' . $this->search . '%')
                        ->orWhere('nombre_rem', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->por_pagina);

        $estatus_disponibles = RecolectaEstatus::where('activo', true)->orderBy('orden')->get();

        return view('livewire.recolectas.recolectas-oficina', compact('recolectas', 'estatus_disponibles'));
    }
}
