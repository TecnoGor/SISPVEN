<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permite paso solo a usuarios con rol "Repartidor Postal Telegrafico" (id=7)
 * o "SuperAdmin" (id=1). Requiere haber pasado por auth:sanctum antes.
 *
 * Verifica por nombre o id para tolerar entornos donde Spatie reasigna IDs.
 */
class EnsureCartero
{
    public const ROLE_NAMES = ['SuperAdmin', 'Repartidor Postal Telegrafico'];
    public const ROLE_IDS = [1, 7];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        if (!method_exists($user, 'roles')) {
            return response()->json(['message' => 'No tiene rol de cartero'], 403);
        }

        $hasRole = $user->roles()
            ->where(function ($q) {
                $q->whereIn('id', self::ROLE_IDS)
                  ->orWhereIn('name', self::ROLE_NAMES);
            })
            ->exists();

        if (!$hasRole) {
            return response()->json(['message' => 'No tiene rol de cartero'], 403);
        }

        return $next($request);
    }
}
