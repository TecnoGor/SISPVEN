<?php

namespace App\Http\Controllers\Api;

use App\Models\Envio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EnvioCedulaController extends Controller
{
    public function buscar(Request $request)
    {
        $request->validate([
            'tipo'   => 'required|in:remitente,destinatario',
            'cedula' => 'required|string',
        ]);

        $query = $request->tipo === 'remitente'
            ? ['documento_rem' => $request->cedula]
            : ['documento_dest' => $request->cedula];

        $envios = Envio::where($query)
            ->whereDoesntHave('envio_encaminamientos', function ($q) {
                $q->where('estatus_id', 17);
            })
            ->select([
                'codigo_envio',
                'nombre_rem',
                'apellido_rem',
                'nombre_dest',
                'apellido_dest',
                'created_at',
            ])
            ->get();

        if ($envios->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "No se encontró ningún envío con la cédula {$request->cedula} como {$request->tipo}.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'envios' => $envios->map(function ($tr) {
                return [
                    'codigo_envio' => $tr->codigo_envio,
                    'nombre_remitente' => $tr->nombre_rem,
                    'apellido_remitente' => $tr->apellido_rem,
                    'nombre_destinatario' => $tr->nombre_dest,
                    'apellido_destinatario' => $tr->apellido_dest,
                    'fecha_consignacion' => $tr->created_at,
                ];
            }),
        ]);
    }
}
