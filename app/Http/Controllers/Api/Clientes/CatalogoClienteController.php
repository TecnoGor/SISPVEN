<?php

namespace App\Http\Controllers\Api\Clientes;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;
use App\Models\Documento;
use App\Models\EntidadBancaria;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\RecolectaEstatus;
use App\Models\Sector;
use App\Models\TipoPago;
use Illuminate\Http\JsonResponse;

/**
 * Catálogos para el formulario de recolecta de la app de clientes.
 * La cascada de ubicación replica exactamente la del módulo Iposplus web:
 * estados (pais 90) → municipios → parroquias + ciudades (ambas hijas de
 * municipio) → códigos postales por parroquia (distinct de sectores).
 */
class CatalogoClienteController extends Controller
{
    private const PAIS_VENEZUELA_ID = 90;

    /** id del tipo de pago "Corporativo", excluido para clientes (patrón CajaDePago). */
    private const TIPO_PAGO_CORPORATIVO_ID = 6;

    /** GET /clientes/v1/catalogs/estados */
    public function estados(): JsonResponse
    {
        $estados = Estado::where('pais_id', self::PAIS_VENEZUELA_ID)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['estado_id', 'nombre']);

        return response()->json([
            'data' => $estados->map(fn ($e) => [
                'id' => (string) $e->estado_id,
                'nombre' => $e->nombre,
            ]),
        ]);
    }

    /** GET /clientes/v1/catalogs/estados/{estado}/municipios */
    public function municipios(int $estado): JsonResponse
    {
        $municipios = Municipio::where('estado_id', $estado)
            ->orderBy('nombre')
            ->get(['municipio_id', 'nombre']);

        return response()->json([
            'data' => $municipios->map(fn ($m) => [
                'id' => (string) $m->municipio_id,
                'nombre' => $m->nombre,
            ]),
        ]);
    }

    /** GET /clientes/v1/catalogs/municipios/{municipio}/parroquias */
    public function parroquias(int $municipio): JsonResponse
    {
        $parroquias = Parroquia::where('municipio_id', $municipio)
            ->orderBy('nombre')
            ->get(['parroquia_id', 'nombre']);

        return response()->json([
            'data' => $parroquias->map(fn ($p) => [
                'id' => (string) $p->parroquia_id,
                'nombre' => $p->nombre,
            ]),
        ]);
    }

    /** GET /clientes/v1/catalogs/municipios/{municipio}/ciudades */
    public function ciudades(int $municipio): JsonResponse
    {
        $ciudades = Ciudad::where('municipio_id', $municipio)
            ->orderBy('nombre')
            ->get(['ciudad_id', 'nombre']);

        return response()->json([
            'data' => $ciudades->map(fn ($c) => [
                'id' => (string) $c->ciudad_id,
                'nombre' => $c->nombre,
            ]),
        ]);
    }

    /** GET /clientes/v1/catalogs/parroquias/{parroquia}/codigos-postales */
    public function codigosPostales(int $parroquia): JsonResponse
    {
        // Réplica de Iposplus::updatedParroquiaDest(): el cliente elige el
        // código postal, nunca el sector.
        $codigos = Sector::where('parroquia_id', $parroquia)
            ->distinct()
            ->pluck('codigo_postal')
            ->sort()
            ->values()
            ->map(fn ($cp) => (string) $cp);

        return response()->json(['data' => $codigos]);
    }

    /** GET /clientes/v1/catalogs/tipos-documento */
    public function tiposDocumento(): JsonResponse
    {
        $documentos = Documento::all(['documento_id', 'tipo', 'descripcion']);

        return response()->json([
            'data' => $documentos->map(fn ($d) => [
                'id' => (string) $d->documento_id,
                'tipo' => $d->tipo,
                'descripcion' => $d->descripcion,
            ]),
        ]);
    }

    /** GET /clientes/v1/catalogs/metodos-pago */
    public function metodosPago(): JsonResponse
    {
        $metodos = TipoPago::where('activo', true)
            ->whereNotIn('tipo_pago_id', [self::TIPO_PAGO_CORPORATIVO_ID])
            ->orderBy('tipo_pago_id')
            ->get(['tipo_pago_id', 'nombre']);

        return response()->json([
            'data' => $metodos->map(fn ($m) => [
                'id' => (string) $m->tipo_pago_id,
                'nombre' => $m->nombre,
            ]),
        ]);
    }

    /** GET /clientes/v1/catalogs/bancos */
    public function bancos(): JsonResponse
    {
        $bancos = EntidadBancaria::orderBy('nombre')->get(['entidad_bancaria_id', 'nombre', 'codigo']);

        return response()->json([
            'data' => $bancos->map(fn ($b) => [
                'id' => (string) $b->entidad_bancaria_id,
                'nombre' => $b->nombre,
                'codigo' => $b->codigo,
            ]),
        ]);
    }

    /** GET /clientes/v1/catalogs/estados-recolecta */
    public function estadosRecolecta(): JsonResponse
    {
        $estados = RecolectaEstatus::where('activo', true)
            ->orderBy('orden')
            ->get(['recolecta_estatus_id', 'nombre', 'slug', 'orden']);

        return response()->json([
            'data' => $estados->map(fn ($e) => [
                'id' => (string) $e->recolecta_estatus_id,
                'nombre' => $e->nombre,
                'slug' => $e->slug,
                'orden' => $e->orden,
            ]),
        ]);
    }
}
