<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Envio;
use Illuminate\Http\Request;
use App\Models\EnvioEncaminamiento;
use App\Http\Controllers\Controller;

class EnvioController extends Controller
{
    public function showPost(Request $request)
    {
        // Validar que venga el parámetro codigo
        $request->validate([
            'codigo' => 'required'
        ]);

        $codigo = $request->input('codigo');

        // Buscar el envío con relación oficinas
        $envio = Envio::with('oficinas')->where('codigo_envio', $codigo)->first();

        if (! $envio) {
            return response()->json([
                'error' => 'Envío no encontrado'
            ], 404);
        }

        // Usuario relacionado
        $usuario = User::find($envio->usuario_id);

        // Encaminamientos ordenados
        $results = EnvioEncaminamiento::where('envio_id', $envio->envio_id)
            ->orderBy('envios_encaminamiento_id')
            ->get();

        if ($results->isEmpty()) {
            return response()->json([
                'codigo_envio' => $envio->codigo_envio,
                'mensaje' => 'El envío no tiene registros de encaminamiento'
            ], 200);
        }

        // Último estado
        $status = EnvioEncaminamiento::where('envio_id', $envio->envio_id)
            ->orderByDesc('created_at')
            ->first();

        // Respuesta JSON
        return response()->json([
            'codigo_envio' => $envio->codigo_envio,
            'tipo_envio' => $envio->tipo_envio,
            'peso' => $envio->peso,
            'remitente' => $envio->nombre_rem . ' ' . $envio->apellido_rem,
            'doc_remitente' => $envio->tipo_documento_rem . ' ' . $envio->documento_rem,
            'correo_remitente' => $envio->correo_rem,
            'origen' => $envio->oficinas->nombre,
            'contenido' => $envio->contenido,
            'costo' => $envio->coste,
            'destinatario' => $envio->nombre_dest . ' ' . $envio->apellido_dest,
            'doc_destinatario' => $envio->tipo_documento_dest . ' ' . $envio->documento_dest,
            'correo_destinatario' => $envio->correo_dest,
            'estatus_actual' => $status?->envio_estatus?->estatus ?? 'Sin estatus',

            'encaminamientos' => $results->map(function ($tr) use ($results) {
                $estatus = $tr->envio_estatus?->estatus;
                return [
                    'oficina' => $tr->oficinas?->nombre,
                    'estatus' => ($estatus === 'Salida hacia COP' && $tr->oficina_externa?->externa)
                        ? 'Salida hacia Aliado'
                        : $estatus,
                    'oficina_externa' => $tr->oficina_externa?->nombre,
                    'fecha' => $tr->created_at?->toDateTimeString(),
                    'ultimo' => $tr->is($results->last()),
                ];
            }),
        ]);
    }
}
