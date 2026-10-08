<?php

namespace App\Listeners;

use App\Helpers\UserAgentParser;
use App\Models\UsuarioSeguimiento;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        if (! $event->user) {
            return;
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => $event->user->id,
            'accion'      => 'login',
            'descripcion' => "El usuario ({$event->user->id}) inició sesión",
            'ip_address'  => Request::ip(),
            'user_agent'  => UserAgentParser::parse(Request::userAgent()),
        ]);
    }
}
