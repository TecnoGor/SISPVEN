<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\OficinaSemaforoPostal;
use Symfony\Component\HttpFoundation\Response;

class OficinaArrendada
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        $user = auth()->user();

        if(!$user || !$user->oficina_id){
            abort(403, 'Acceso denegado: No posee una oficina asignada');
        }

        $acceso = OficinaSemaforoPostal::where('oficina_id', $user->oficina_id)->where('condicion', 'Arrendada')->exists();

        if(!$acceso){
            abort(403, 'Acceso denegado: la oficina no se encuentra en condicion de arrendamiento');
        }


        return $next($request);
    }
}
