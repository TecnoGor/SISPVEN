<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe a SuperAdmin. Verifica por nombre o id.
 */
class EnsureCarteroAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        if (!method_exists($user, 'roles')) {
            return response()->json(['message' => 'Requiere rol administrador'], 403);
        }

        $isAdmin = $user->roles()
            ->where(function ($q) {
                $q->where('id', 1)->orWhere('name', 'SuperAdmin');
            })
            ->exists();

        if (!$isAdmin) {
            return response()->json(['message' => 'Requiere rol administrador'], 403);
        }

        return $next($request);
    }
}
