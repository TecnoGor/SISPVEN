<?php

namespace App\Http\Controllers\Api\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clientes\CrearRecolectaRequest;
use App\Models\Oficina;
use App\Models\Recolecta;
use App\Models\RecolectaEstatus;
use App\Notifications\RecolectaSolicitadaNotification;
use App\Services\Recolectas\OficinaRecolectoraResolver;
use App\Services\Recolectas\RecolectaFlowService;
use App\Services\Recolectas\SinTarifaDisponibleException;
use App\Services\Recolectas\TarifaRecolectaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Recolectas Iposplus de la app de clientes: cotización, creación,
 * listado y cancelación. El dueño de cada recolecta es el usuario de la
 * app (sispven_app.usuarios) autenticado por token.
 */
class RecolectaClienteController extends Controller
{
    public function __construct(
        private TarifaRecolectaService $tarifas,
        private OficinaRecolectoraResolver $oficinas,
        private RecolectaFlowService $flow,
    ) {}

    /** POST /clientes/v1/recolectas/cotizar */
    public function cotizar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'modo_peso' => 'required|in:manual,volumetrico',
            'peso' => 'nullable|required_if:modo_peso,manual|numeric|min:0.001',
            'alto' => 'nullable|required_if:modo_peso,volumetrico|numeric|min:0.001|max:300',
            'ancho' => 'nullable|required_if:modo_peso,volumetrico|numeric|min:0.001|max:300',
            'largo' => 'nullable|required_if:modo_peso,volumetrico|numeric|min:0.001|max:300',
            'municipio_id' => 'required|integer|exists:municipios,municipio_id',
            'parroquia_id' => 'required|integer|exists:parroquias,parroquia_id',
        ]);

        if ($data['modo_peso'] === 'volumetrico') {
            $suma = (float) ($data['alto'] ?? 0) + (float) ($data['ancho'] ?? 0) + (float) ($data['largo'] ?? 0);
            if ($suma > 300) {
                return response()->json([
                    'message' => 'La suma de alto, ancho y largo no debe superar 300 cm.',
                ], 422);
            }
        }

        $oficina = $this->oficinas->resolver((int) $data['municipio_id'], (int) $data['parroquia_id']);
        if (!$oficina) {
            return response()->json([
                'message' => 'No hay oficina de Ipostel que atienda recolectas en tu municipio.',
            ], 422);
        }

        try {
            $cotizacion = $this->tarifas->cotizar(
                $data['modo_peso'],
                isset($data['peso']) ? (float) $data['peso'] : null,
                isset($data['alto']) ? (float) $data['alto'] : null,
                isset($data['ancho']) ? (float) $data['ancho'] : null,
                isset($data['largo']) ? (float) $data['largo'] : null,
                $oficina
            );
        } catch (SinTarifaDisponibleException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'data' => $cotizacion + [
                'oficina' => [
                    'id' => (string) $oficina->oficina_id,
                    'nombre' => $oficina->nombre,
                    'zona_economica_especial' => (bool) $oficina->zona_economica_especial,
                ],
            ],
        ]);
    }

    /** POST /clientes/v1/recolectas */
    public function store(CrearRecolectaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usuario = $request->user();

        $oficina = $this->oficinas->resolver((int) $data['municipio_id'], (int) $data['parroquia_id']);
        if (!$oficina) {
            return response()->json([
                'message' => 'No hay oficina de Ipostel que atienda recolectas en tu municipio.',
            ], 422);
        }

        // Cotización server-side: fuente de verdad, ignora cualquier monto
        // que envíe el cliente.
        try {
            $cotizacion = $this->tarifas->cotizar(
                $data['modo_peso'],
                isset($data['peso']) ? (float) $data['peso'] : null,
                isset($data['alto']) ? (float) $data['alto'] : null,
                isset($data['ancho']) ? (float) $data['ancho'] : null,
                isset($data['largo']) ? (float) $data['largo'] : null,
                $oficina
            );
        } catch (SinTarifaDisponibleException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $recolecta = DB::transaction(function () use ($data, $usuario, $oficina, $cotizacion) {
            $recolecta = Recolecta::create([
                'usuario_app_id' => $usuario->usuario_id,
                'oficina_id' => $oficina->oficina_id,
                'recolecta_estatus_id' => RecolectaEstatus::porSlug(RecolectaEstatus::SOLICITADA)->recolecta_estatus_id,
                'codigo' => $this->generarCodigo(),

                'nombre_rem' => $data['nombre_rem'],
                'apellido_rem' => $data['apellido_rem'],
                'tipo_documento_rem' => $data['tipo_documento_rem'],
                'documento_rem' => $data['documento_rem'],
                'telefono_rem' => $data['telefono_rem'],
                'correo_rem' => $data['correo_rem'],

                'estado_id' => $data['estado_id'],
                'municipio_id' => $data['municipio_id'],
                'parroquia_id' => $data['parroquia_id'],
                'ciudad_id' => $data['ciudad_id'],
                'codigo_postal' => (string) $data['codigo_postal'],
                'direccion' => $data['direccion'],
                'referencia' => $data['referencia'] ?? null,
                'latitud' => $data['latitude'],
                'longitud' => $data['longitude'],
                'precision_gps' => $data['gps_accuracy'] ?? null,
                'gps_manual' => (bool) ($data['gps_manual'] ?? false),

                'nombre_dest' => $data['nombre_dest'],
                'apellido_dest' => $data['apellido_dest'],
                'tipo_documento_dest' => $data['tipo_documento_dest'],
                'documento_dest' => $data['documento_dest'],
                'telefono_dest' => $data['telefono_dest'],
                'correo_dest' => $data['correo_dest'],
                'estado_dest_id' => $data['estado_dest_id'],
                'municipio_dest_id' => $data['municipio_dest_id'],
                'parroquia_dest_id' => $data['parroquia_dest_id'],
                'ciudad_dest_id' => $data['ciudad_dest_id'],
                'codigo_postal_dest' => (string) $data['codigo_postal_dest'],
                'direccion_dest' => $data['direccion_dest'],

                'modo_peso' => $data['modo_peso'],
                'peso' => $cotizacion['peso_facturable'],
                'alto' => $data['alto'] ?? null,
                'ancho' => $data['ancho'] ?? null,
                'largo' => $data['largo'] ?? null,
                'contenido' => $data['contenido'],

                'monto_envio' => $cotizacion['monto_envio'],
                'monto_recoleccion' => $cotizacion['monto_recoleccion'],
                'iva' => $cotizacion['iva'],
                'total' => $cotizacion['total'],
                'tasa_bs' => $cotizacion['tasa_bs'],
                'excede_tarifa_max' => $cotizacion['excede_tarifa_max'],
            ]);

            // Notificar a la oficina recolectora dentro de la transacción
            // (paso que el precedente de telegramas APP- omitió).
            $oficina->notify(new RecolectaSolicitadaNotification($recolecta));

            return $recolecta;
        });

        return response()->json(['data' => $this->serializarDetalle($recolecta->load('estatus', 'oficina'))], 201);
    }

    /** GET /clientes/v1/recolectas */
    public function index(Request $request): JsonResponse
    {
        $paginado = Recolecta::deUsuarioApp($request->user()->usuario_id)
            ->with('estatus', 'oficina')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'data' => collect($paginado->items())->map(fn ($r) => $this->serializarResumen($r)),
            'meta' => [
                'current_page' => $paginado->currentPage(),
                'last_page' => $paginado->lastPage(),
                'total' => $paginado->total(),
            ],
        ]);
    }

    /** GET /clientes/v1/recolectas/{id} */
    public function show(Request $request, int $id): JsonResponse
    {
        $recolecta = $this->recolectaDelUsuario($request, $id);

        return response()->json([
            'data' => $this->serializarDetalle($recolecta->load('estatus', 'oficina', 'pagos.tipoPago', 'envio')),
        ]);
    }

    /** POST /clientes/v1/recolectas/{id}/cancelar */
    public function cancelar(Request $request, int $id): JsonResponse
    {
        $recolecta = $this->recolectaDelUsuario($request, $id);

        if (!in_array($recolecta->estatus->slug, RecolectaFlowService::CANCELABLES_POR_CLIENTE, true)) {
            return response()->json([
                'message' => 'La recolecta ya no puede cancelarse en su estado actual.',
            ], 422);
        }

        $this->flow->transicionar($recolecta, RecolectaEstatus::CANCELADA);

        return response()->json(['data' => $this->serializarDetalle($recolecta->load('estatus', 'oficina'))]);
    }

    private function recolectaDelUsuario(Request $request, int $id): Recolecta
    {
        return Recolecta::deUsuarioApp($request->user()->usuario_id)
            ->where('recolecta_id', $id)
            ->firstOrFail();
    }

    private function generarCodigo(): string
    {
        $prefijo = config('recolectas.prefijo_codigo');
        $ultimo = Recolecta::orderByDesc('recolecta_id')->value('recolecta_id') ?? 0;

        return $prefijo . str_pad($ultimo + 1, 8, '0', STR_PAD_LEFT);
    }

    private function serializarResumen(Recolecta $r): array
    {
        return [
            'id' => (string) $r->recolecta_id,
            'codigo' => $r->codigo,
            'estatus' => $r->estatus->slug,
            'estatus_nombre' => $r->estatus->nombre,
            'contenido' => $r->contenido,
            'peso' => (string) $r->peso,
            'total' => (string) $r->total,
            'oficina' => $r->oficina?->nombre,
            'codigo_envio' => $r->envio_id ? $r->envio?->codigo_envio : null,
            'created_at' => $r->created_at?->toIso8601String(),
        ];
    }

    private function serializarDetalle(Recolecta $r): array
    {
        return $this->serializarResumen($r) + [
            'destinatario' => [
                'nombre' => $r->nombre_dest,
                'apellido' => $r->apellido_dest,
                'direccion' => $r->direccion_dest,
            ],
            'punto_recolecta' => [
                'direccion' => $r->direccion,
                'referencia' => $r->referencia,
                'latitude' => $r->latitud !== null ? (float) $r->latitud : null,
                'longitude' => $r->longitud !== null ? (float) $r->longitud : null,
                'gps_manual' => (bool) $r->gps_manual,
            ],
            'montos' => [
                'monto_envio' => (string) $r->monto_envio,
                'monto_recoleccion' => (string) $r->monto_recoleccion,
                'iva' => (string) $r->iva,
                'total' => (string) $r->total,
                'tasa_bs' => $r->tasa_bs !== null ? (string) $r->tasa_bs : null,
            ],
            'pago_confirmado_en' => $r->pago_confirmado_en?->toIso8601String(),
            'motivo_rechazo' => $r->motivo_rechazo,
            'pagos' => $r->relationLoaded('pagos') ? $r->pagos->map(fn ($p) => [
                'id' => (string) $p->recolecta_pago_id,
                'tipo_pago' => $p->tipoPago?->nombre,
                'monto' => (string) $p->monto,
                'numero_referencia' => $p->numero_referencia,
                'estatus' => $p->estatus,
                'fecha_pago' => $p->fecha_pago?->toDateString(),
            ])->all() : [],
        ];
    }
}
