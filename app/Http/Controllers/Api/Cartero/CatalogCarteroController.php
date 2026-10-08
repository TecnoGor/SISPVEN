<?php

namespace App\Http\Controllers\Api\Cartero;

use App\Http\Controllers\Controller;
use App\Models\MotivoDevolucion;
use Illuminate\Http\JsonResponse;

class CatalogCarteroController extends Controller
{
    /** GET /cartero/v1/catalogs/return-reasons */
    public function returnReasons(): JsonResponse
    {
        $motivos = MotivoDevolucion::where('activo', true)
            ->orderBy('id')
            ->get(['id', 'codigo', 'nombre']);

        return response()->json([
            'data' => $motivos->map(fn ($m) => [
                'id' => (string) $m->id,
                'codigo' => $m->codigo,
                'nombre' => $m->nombre,
            ]),
        ]);
    }
}
