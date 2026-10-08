<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Centro de notificaciones del header. Agnóstico del módulo: une las notificaciones
 * del usuario autenticado y las de su oficina, y las pinta desde su payload común
 * (tipo/label/ruta/icono). Cualquier módulo que emita una notificación con ese
 * contrato aparece aquí sin tocar este componente.
 */
class CentroNotificaciones extends Component
{
    /**
     * Refresca el badge cuando otro componente marca algo como recibido/leído.
     * El método está vacío a propósito: el evento fuerza el re-render y el conteo
     * se recalcula en render().
     */
    #[On('telegrama-confirmado')]
    #[On('notificacion-leida')]
    public function refrescar()
    {
        // no-op: solo dispara el re-render.
    }

    /**
     * Permiso requerido para ver cada tipo de notificación. Si un tipo no está aquí,
     * se muestra sin restricción. Un usuario sin el permiso no ve (ni cuenta) esas
     * notificaciones, aunque existan para su oficina.
     */
    protected function permisoPorTipo(): array
    {
        return [
            'telegrama' => 'Ver telegramas',
            'recolecta' => 'Ver recolectas',
        ];
    }

    /**
     * Notificaciones no leídas del usuario actual: las suyas (personales) más las
     * de su oficina, filtradas por permiso según su tipo, ordenadas de más reciente
     * a más antigua.
     */
    protected function notificacionesNoLeidas()
    {
        $user = Auth::user();

        $propias = $user->unreadNotifications;
        $deOficina = optional($user->oficina)->unreadNotifications ?? collect();

        $permisos = $this->permisoPorTipo();

        return $propias->concat($deOficina)
            ->filter(function ($notif) use ($user, $permisos) {
                $tipo = $notif->data['tipo'] ?? null;
                $permiso = $permisos[$tipo] ?? null;

                // Sin permiso asociado → visible; con permiso asociado → solo si lo tiene.
                return $permiso === null || $user->can($permiso);
            })
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Marca una notificación como leída (esté en el usuario o en su oficina).
     * Recordar el matiz: una notificación de oficina se marca leída para toda la
     * oficina (comportamiento esperado en telegramas).
     *
     * Las notificaciones de telegrama NO se marcan aquí al hacer clic: se marcan al
     * confirmar el telegrama en la pantalla de recepción. Este método queda disponible
     * para tipos de notificación futuros cuya semántica sí sea "leída al abrirla".
     */
    public function marcarLeida(string $id)
    {
        $user = Auth::user();

        $notificacion = $user->notifications()->find($id)
            ?? optional($user->oficina)->notifications()->find($id);

        $notificacion?->markAsRead();
    }

    public function render()
    {
        $notificaciones = $this->notificacionesNoLeidas();

        return view('livewire.centro-notificaciones', [
            'notificaciones'   => $notificaciones,
            'telegramasCount'  => $notificaciones->count(),
        ]);
    }
}
