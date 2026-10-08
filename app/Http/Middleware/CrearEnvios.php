<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UsuarioCierre;
use Illuminate\Support\Facades\Auth;

class CrearEnvios
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !UsuarioCierre::where('usuario_id', Auth::user()->id)->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->exists()) {
            return $next($request);
        }

        return redirect()->route('dashboard');
    }
}
