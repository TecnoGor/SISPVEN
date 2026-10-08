<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Telegramas\ConsignarTelegramaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TelegramaFlutterController extends Controller
{
    /**
     * GET /api/telegramas/catalogos
     * Catálogos públicos para la app Flutter de telegramas.
     */
    public function catalogos(): JsonResponse
    {
        try {
            $tiposRemitente = DB::table('tipos_remitente_telegramas')
                ->select('tipos_remitente_telegramas_id', 'nombre')
                ->orderBy('tipos_remitente_telegramas_id')
                ->get();

            $lugaresEmision = DB::table('lugar_emision_telegramas')
                ->select('lugar_emision_telegramas_id', 'nombre')
                ->orderBy('lugar_emision_telegramas_id')
                ->get();

            $circuitos = DB::table('circuito_judicial_tribunal_telegramas')
                ->select('circuito_judicial_tribunal_telegramas_id', 'lugar_emision_telegramas_id', 'nombre', 'activo')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'tipos_remitente'      => $tiposRemitente,
                    'lugares_emision'      => $lugaresEmision,
                    'circuitos_judiciales' => $circuitos,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('TELEGRAMAS FLUTTER CATALOGOS ERROR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener catálogos',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/telegramas/consignar  (auth:sanctum)
     * Crea un telegrama desde la app Flutter.
     */
    public function consignar(ConsignarTelegramaRequest $request): JsonResponse
    {
        $usuario = $request->user();

        DB::beginTransaction();
        try {
            // Código único para envíos desde app móvil
            $totalApp    = DB::table('envios')->where('codigo_envio', 'like', 'APP-%')->count();
            $codigoEnvio = 'APP-' . str_pad($totalApp + 1, 8, '0', STR_PAD_LEFT);

            // Buscar oficina destino (opcional)
            $oficinaDest = null;
            if (!empty($request->oficina_destino_nombre)) {
                $oficinaDest = DB::table('oficinas')
                    ->whereRaw("LOWER(nombre) LIKE ?", ['%' . strtolower($request->oficina_destino_nombre) . '%'])
                    ->first();
            }

            $rem  = $request->remitente;
            $dest = $request->destinatario;

            // Las columnas *_dest de ubicación en `envios` son FKs bigint a los
            // catálogos (estados/municipios/parroquias/ciudades), pero la app
            // envía NOMBRES en texto libre. Insertar el texto directo revienta
            // el insert en PostgreSQL ("invalid input syntax for type bigint"),
            // que era la causa del "Error al consignar el telegrama" cuando el
            // usuario llenaba la ubicación. Se resuelve cada nombre contra su
            // catálogo (en cascada, para no matchear homónimos de otro estado);
            // si no hay coincidencia queda null — la dirección completa ya
            // conserva los textos tal cual los escribió el usuario.
            $estadoDestId    = $this->buscarIdPorNombre('estados', 'estado_id', $dest['estado_nombre'] ?? null);
            $municipioDestId = $this->buscarIdPorNombre('municipios', 'municipio_id', $dest['municipio'] ?? null, 'estado_id', $estadoDestId);
            $parroquiaDestId = $this->buscarIdPorNombre('parroquias', 'parroquia_id', $dest['parroquia'] ?? null, 'municipio_id', $municipioDestId);
            $ciudadDestId    = $this->buscarIdPorNombre('ciudades', 'ciudad_id', $dest['ciudad'] ?? null, 'municipio_id', $municipioDestId);

            // codigo_postal_dest es integer en `envios`; la app lo envía como texto.
            $codigoPostalDest = isset($dest['codigo_postal']) && is_numeric($dest['codigo_postal'])
                ? (int) $dest['codigo_postal']
                : null;

            $envioId = DB::table('envios')->insertGetId([
                'servicio_id'         => 3,
                'tipo_envio'          => 'nacional',
                'oficina_id'          => null,
                // NOTA: envios.usuario_id tiene FK a `users` (sistema web), pero los
                // usuarios de la app móvil viven en `sispven_app.usuarios` con IDs
                // que no se cruzan. Por eso queda null. El owner del telegrama se
                // identifica por documento_rem + tipo_documento_rem en misEnviados.
                'usuario_id'          => null,
                'nombre_rem'          => $rem['nombre'],
                'apellido_rem'        => $rem['apellido'],
                'tipo_documento_rem'  => $rem['tipo_documento'],
                'documento_rem'       => $rem['numero_documento'],
                'correo_rem'          => $rem['correo'] ?? null,
                'telefono_rem'        => $rem['telefono'] ?? null,
                'nombre_dest'         => $dest['nombre'],
                'apellido_dest'       => $dest['apellido'],
                'tipo_documento_dest' => $dest['tipo_documento'],
                'documento_dest'      => $dest['numero_documento'],
                'correo_dest'         => $dest['correo'] ?? null,
                'tlf_dest'            => $dest['telefono'] ?? null,
                'estado_dest'         => $estadoDestId,
                'municipio_dest'      => $municipioDestId,
                'parroquia_dest'      => $parroquiaDestId,
                'ciudad_dest'         => $ciudadDestId,
                'codigo_postal_dest'  => $codigoPostalDest,
                'oficina_dest_id'     => $oficinaDest ? $oficinaDest->oficina_id : null,
                'direccion_dest'      => $dest['direccion'],
                'coste'               => $request->total_a_pagar,
                'coste_sin_iva'       => $request->costo_sin_iva,
                'codigo_envio'        => $codigoEnvio,
                // devolucion y descubierto son NOT NULL sin default en `envios`.
                // El flujo web (Telegrama.php) los manda en false; aquí también.
                'devolucion'          => false,
                'descubierto'         => false,
                'created_at'          => now(),
                'updated_at'          => now(),
            // El segundo argumento es OBLIGATORIO aquí: en PostgreSQL,
            // insertGetId() genera RETURNING "id" por defecto, pero la PK de
            // `envios` se llama envio_id. Sin esto Postgres responde
            // 'column "id" does not exist' y el insert falla SIEMPRE.
            ], 'envio_id');

            DB::table('telegramas_recibidos')->insert([
                'envio_id'                 => $envioId,
                'recibido'                 => false,
                'tipo_remitente'           => $request->tipo_remitente_id ?? null,
                'lugar_emision_rem'        => $request->lugar_emision_id ?? null,
                'sitio_especifico_emision' => $request->circuito_judicial_id ?? null,
                'circuito_judicial_dest'   => $request->circuito_judicial_dest_id ?? null,
                'contenido_telegrama'      => $request->texto_telegrama,
                'palabras_tasables'        => $request->palabras_tasables,
                'palabras_reales'          => $request->palabras_reales,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Telegrama consignado exitosamente',
                'data'    => [
                    'envio_id'     => $envioId,
                    'codigo_envio' => $codigoEnvio,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('TELEGRAMAS FLUTTER CONSIGNAR ERROR: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al consignar el telegrama',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/telegramas/mis-enviados  (auth:sanctum)
     * Telegramas enviados por el usuario autenticado.
     */
    public function misEnviados(Request $request): JsonResponse
    {
        try {
            $usuario = $request->user();

            // Identificación del owner por documento_rem + tipo_documento_rem.
            // No usamos envios.usuario_id porque su FK apunta a `users` (sistema web)
            // y los usuarios de la app viven en `sispven_app.usuarios` (IDs disjuntos).
            // El lado app pre-pobla el remitente con los datos del usuario logueado
            // para que esta coincidencia sea exacta.
            $data = DB::table('envios as e')
                ->leftJoin('telegramas_recibidos as tr', 'tr.envio_id', '=', 'e.envio_id')
                ->leftJoin('oficinas as od', 'od.oficina_id', '=', 'e.oficina_dest_id')
                ->where('e.servicio_id', 3)
                ->where('e.documento_rem', (string) $usuario->cedula)
                ->where('e.tipo_documento_rem', $usuario->tipo_documento)
                ->select([
                    'e.envio_id',
                    'e.codigo_envio',
                    'e.nombre_rem', 'e.apellido_rem',
                    'e.documento_rem', 'e.tipo_documento_rem',
                    'e.nombre_dest', 'e.apellido_dest',
                    'e.documento_dest', 'e.tipo_documento_dest',
                    'e.estado_dest', 'e.ciudad_dest', 'e.direccion_dest',
                    'e.coste', 'e.coste_sin_iva',
                    'od.nombre as nombre_oficina_dest',
                    'tr.contenido_telegrama',
                    'tr.palabras_tasables', 'tr.palabras_reales',
                    'tr.recibido',
                    'e.created_at',
                ])
                ->orderBy('e.created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('TELEGRAMAS FLUTTER MIS-ENVIADOS ERROR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener telegramas enviados',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/telegramas/mis-recibidos  (auth:sanctum)
     * Telegramas recibidos por el usuario autenticado.
     */
    public function misRecibidos(Request $request): JsonResponse
    {
        try {
            $usuario = $request->user();

            // Recibidos identifican al destinatario por cédula+tipo_documento (no por usuario_id,
            // porque el destinatario puede no estar registrado en la app).
            $data = DB::table('envios as e')
                ->leftJoin('telegramas_recibidos as tr', 'tr.envio_id', '=', 'e.envio_id')
                ->leftJoin('oficinas as oo', 'oo.oficina_id', '=', 'e.oficina_id')
                ->where('e.servicio_id', 3)
                ->where('e.documento_dest', (string) $usuario->cedula)
                ->where('e.tipo_documento_dest', $usuario->tipo_documento)
                ->select([
                    'e.envio_id',
                    'e.codigo_envio',
                    'e.nombre_rem', 'e.apellido_rem',
                    'e.documento_rem', 'e.tipo_documento_rem',
                    'e.nombre_dest', 'e.apellido_dest',
                    'e.documento_dest', 'e.tipo_documento_dest',
                    'e.estado_dest', 'e.ciudad_dest', 'e.direccion_dest',
                    'e.coste', 'e.coste_sin_iva',
                    'oo.nombre as nombre_oficina_origen',
                    'tr.contenido_telegrama',
                    'tr.palabras_tasables', 'tr.palabras_reales',
                    'tr.recibido',
                    'e.created_at',
                ])
                ->orderBy('e.created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('TELEGRAMAS FLUTTER MIS-RECIBIDOS ERROR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener telegramas recibidos',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Resuelve el id de un catálogo de ubicación a partir del nombre en texto
     * libre que envía la app. Comparación case-insensitive; si se conoce el
     * padre de la cascada, se exige (evita matchear homónimos de otro estado
     * o municipio — sin padre resuelto, el hijo queda null a propósito).
     */
    private function buscarIdPorNombre(
        string $tabla,
        string $columnaId,
        ?string $nombre,
        ?string $columnaPadre = null,
        ?int $padreId = null
    ): ?int {
        $nombre = trim((string) $nombre);
        if ($nombre === '') {
            return null;
        }

        if ($columnaPadre !== null && $padreId === null) {
            return null;
        }

        $query = DB::table($tabla)->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)]);
        if ($columnaPadre !== null) {
            $query->where($columnaPadre, $padreId);
        }

        $id = $query->value($columnaId);

        return $id !== null ? (int) $id : null;
    }
}
