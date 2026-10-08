<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Oficina;

class VenezuelaController extends Controller
{
    public function oficinaMapa($code) {
        $user = auth()->user();
        if ($user && !$user->hasRole(['Presidente', 'SuperAdmin', 'CNC'])) {
            $estadoUsuario = $user->oficina->estado_id ?? null;
            $allowed = [$estadoUsuario];
            if ($estadoUsuario == 1) { 
                $allowed = array_merge($allowed, [2, 24]); 
            } elseif ($estadoUsuario == 20) { 
                $allowed = array_merge($allowed, [21]); 
            }

            if (!in_array($code, $allowed)) {
                return response()->json(['message' => 'No autorizado', 'status' => '403'], 403);
            }
        }

        $oficinas = Oficina::where('externa', false)
            ->whereNotIn('estatus_id', [2, 3])
            ->where('estado_id', $code)
            ->get(['nombre', 'operaciones', 'tipo_oficina_id']);
        
        if ($oficinas->isEmpty()) {
            $data = [
                'message' => 'Oficina no encontrado',
                'status' => '404'               
            ];
            return response()->json($data, 404);
        }

        $data = [
            'Nombre' => $oficinas->pluck('nombre'),
            'Operaciones' => $oficinas->pluck('operaciones'),
            'Tipo' => $oficinas->pluck('tipo_oficina_id'),
            'status' => '200',                
        ];

        return response()->json($data, 200);

    }
    /**
     * Conteo de oficinas por estado (total y operativas) en UNA sola consulta con
     * GROUP BY, para colorear el mapa sin hacer un fetch por estado (antes: 26 requests).
     * Devuelve por estado el total, las activas y el color ya resuelto, para que el
     * frontend solo pinte.
     */
    public function mapaColores() {
        $user = auth()->user();

        $query = Oficina::where('externa', false)->whereNotIn('estatus_id', [2, 3]);

        // Restringir a los estados del usuario si no es rol global.
        if ($user && !$user->hasRole(['Presidente', 'SuperAdmin', 'CNC'])) {
            $estadoUsuario = $user->oficina->estado_id ?? null;
            $allowed = [$estadoUsuario];
            if ($estadoUsuario == 1) {
                $allowed = array_merge($allowed, [2, 24]);
            } elseif ($estadoUsuario == 20) {
                $allowed = array_merge($allowed, [21]);
            }
            $query->whereIn('estado_id', $allowed);
        }

        $conteos = $query
            ->select(
                'estado_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(*) FILTER (WHERE operaciones = true) as activas')
            )
            ->groupBy('estado_id')
            ->get();

        $data = [];
        foreach ($conteos as $fila) {
            $total = (int) $fila->total;
            $activas = (int) $fila->activas;
            // Misma lógica de color que el frontend: verde si al menos la mitad opera,
            // rojo si hay oficinas pero menos de la mitad, blanco si no hay.
            $color = $activas >= $total / 2 ? 'verde' : ($total > 0 ? 'rojo' : 'blanco');

            $data[$fila->estado_id] = [
                'total'   => $total,
                'activas' => $activas,
                'color'   => $color,
            ];
        }

        return response()->json($data, 200);
    }

    public function oficinatabla(Request $request) {
        // Solo accesible vía AJAX (evita que se abra el JSON pegando la URL en el navegador)
        if (!$request->ajax()) {
            abort(404);
        }
        $user = auth()->user();
        if ($user && !$user->hasRole(['Presidente', 'SuperAdmin', 'CNC'])) {
            $estadoUsuario = $user->oficina->estado_id ?? null;
            $allowed = [$estadoUsuario];
            if ($estadoUsuario == 1) { 
                $allowed = array_merge($allowed, [2, 24]); 
            } elseif ($estadoUsuario == 20) { 
                $allowed = array_merge($allowed, [21]); 
            }
            
            $seg = Oficina::where('externa', false)
                ->whereNotIn('estatus_id', [2, 3])
                ->whereIn('estado_id', $allowed)
                ->get();
        } else {
            $seg = Oficina::where('externa', false)
                ->whereNotIn('estatus_id', [2, 3])
                ->get();
        }
        
        if ($seg->isEmpty()) {
            $data = [
                'message' => 'No hay datos',
                'status' => '200',                
            ];
            return response()->json($data, 200);
        }

        return response()->json($seg, 200);

    }

}
