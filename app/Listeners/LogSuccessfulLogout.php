<?php

namespace App\Listeners;

use App\Helpers\UserAgentParser;
use App\Models\UsuarioSeguimiento;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => $event->user->id,
            'accion'      => 'logout',
            'descripcion' => "El usuario ({$event->user->id}) cerró sesión",
            'ip_address'  => Request::ip(),
            'user_agent'  => UserAgentParser::parse(Request::userAgent()),
        ]);
    }
}
