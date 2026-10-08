<?php

namespace App\Listeners;

use App\Helpers\UserAgentParser;
use App\Models\UsuarioSeguimiento;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Request;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $email_intentado = $event->credentials['email'] ?? 'desconocido';
        $usuario_id      = $event->user?->id;

        $descripcion = $usuario_id
            ? "Intento de inicio de sesión fallido para el usuario ({$usuario_id}) con el correo ({$email_intentado})"
            : "Intento de inicio de sesión fallido con el correo ({$email_intentado})";

        UsuarioSeguimiento::create([
            'usuario_id'  => $usuario_id,
            'accion'      => 'login_failed',
            'descripcion' => $descripcion,
            'ip_address'  => Request::ip(),
            'user_agent'  => UserAgentParser::parse(Request::userAgent()),
        ]);
    }
}
