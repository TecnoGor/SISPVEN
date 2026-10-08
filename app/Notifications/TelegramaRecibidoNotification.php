<?php

namespace App\Notifications;

use App\Models\Envio;
use Illuminate\Notifications\Notification;

/**
 * Notificación interna (badge del header) que avisa a la oficina destino
 * que llegó un telegrama nuevo pendiente de recibir.
 *
 * Destinatario: la Oficina destino (semántica compartida — la ven todos sus
 * usuarios y se marca leída para toda la oficina, igual que el contador actual).
 *
 * Canal: solo 'database' por ahora. El aviso por correo al ciudadano es un caso
 * aparte (ver docs/notas/plan-notificaciones-laravel.md).
 */
class TelegramaRecibidoNotification extends Notification
{
    public function __construct(
        public Envio $envio
    ) {}

    /**
     * Canales de envío. Solo BD hasta tener infraestructura de correo/colas.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Payload común del centro de notificaciones. TODO módulo que quiera aparecer
     * en el badge debe respetar estas claves: tipo, label, ruta, icono.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'  => 'telegrama',
            'label' => 'telegrama nuevo',
            'ruta'  => route('confirmacion-telegrama'),
            'icono' => 'telegrama',
            'envio_id' => $this->envio->envio_id,
            'codigo_envio' => $this->envio->codigo_envio,
        ];
    }
}