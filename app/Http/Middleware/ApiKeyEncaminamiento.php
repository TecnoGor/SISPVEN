<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyEncaminamiento
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $headerRecibido = $request->header('X-API-KEY');
        $envValor = env('API_KEY');

        if (! Hash::check($headerRecibido, $envValor)) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        return $next($request);
    }
}
