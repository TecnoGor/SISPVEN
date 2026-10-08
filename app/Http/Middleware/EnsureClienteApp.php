<?php

namespace App\Http\Middleware;

use App\Models\UsuarioAppMovil;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permite paso solo a clientes de la app móvil (sispven_app.usuarios)
 * autenticados con un access token de la API de clientes. Requiere haber
 * pasado por auth:sanctum antes.
 *
 * Rechaza tokens de otras poblaciones (users web/cartero) y el refresh
 * token usado como bearer (ability cliente:access obligatoria).
 */
class EnsureClienteApp
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        if (!$user instanceof UsuarioAppMovil) {
            return response()->json(['message' => 'Token no válido para la app de clientes'], 403);
        }

        if (!$user->activo) {
            return response()->json(['message' => 'Cuenta desactivada'], 403);
        }

        if (!$request->user()->tokenCan('cliente:access')) {
            return response()->json(['message' => 'Token no válido para la app de clientes'], 403);
        }

        return $next($request);
    }
}
