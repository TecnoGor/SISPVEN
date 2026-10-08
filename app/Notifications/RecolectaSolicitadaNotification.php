<?php

namespace App\Notifications;

use App\Models\Recolecta;
use Illuminate\Notifications\Notification;

/**
 * Notificación interna (badge del header) que avisa a la oficina recolectora
 * que un cliente de la app solicitó una recolecta nueva.
 *
 * Destinatario: la Oficina resuelta por municipio/parroquia (semántica
 * compartida: la ven todos sus usuarios). Canal: solo 'database'.
 * Contrato de payload del centro de notificaciones: tipo, label, ruta, icono.
 */
class RecolectaSolicitadaNotification extends Notification
{
    public function __construct(
        public Recolecta $recolecta
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo' => 'recolecta',
            'label' => 'recolecta nueva',
            'ruta' => route('recolectas-oficina'),
            'icono' => 'recolecta',
            'recolecta_id' => $this->recolecta->recolecta_id,
            'codigo' => $this->recolecta->codigo,
        ];
    }
}
