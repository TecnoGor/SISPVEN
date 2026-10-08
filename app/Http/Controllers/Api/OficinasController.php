<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Oficina;
use App\Models\Estado;

class OficinasController extends Controller
{
    public function index () {
        $oficinas = Oficina::all();
        
        if ($oficinas->isEmpty()) {
            return response()->json([], 200);
        }

        // Formato compatible con contrato SISPVEN
        return response()->json($oficinas->map(function ($oficina) {
            $municipio = $oficina->municipio;
            $codigoPostal = $oficina->codigos_postales->first();
            return [
                'id' => $oficina->oficina_id,
                'nombre' => $oficina->nombre,
                'estado_id' => $oficina->estado_id,
                'ciudad' => $municipio ? $municipio->nombre : null,
                'direccion' => $oficina->direccion,
                'telefono' => $oficina->telefono,
                'codigo_postal' => $codigoPostal ? $codigoPostal->codigo : null,
            ];
        }), 200);
    }

    public function show($id) {
        $seg = Oficina::find($id);

        if (!$seg) {
            $data = [
                'message' => 'Encaminamiento no encontrado',
                'status' => '404'               
            ];
            return response()->json($data, 404);
        }
        $data = [
                'envio' => $seg,
                'status' => '200',                
            ];

            return response()->json($data, 200);
    }

    public function estados() {
        $estados = Estado::all();

        if ($estados->isEmpty()) {
            return response()->json([], 200);
        }

        // Formato compatible con contrato SISPVEN: array con id y nombre
        return response()->json($estados->map(function ($estado) {
            return [
                'id' => $estado->estado_id,
                'nombre' => $estado->nombre,
            ];
        }), 200);
    }

    public function oficinas_relacionadas($estado) {

        $oficinas = Oficina::where('estado_id', $estado)->get();

        if ($oficinas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Estado no encontrado',
            ], 404);
        }

        // Formato compatible con contrato SISPVEN
        return response()->json($oficinas->map(function ($oficina) {
            $municipio = $oficina->municipio;
            $codigoPostal = $oficina->codigos_postales->first();
            return [
                'id' => $oficina->oficina_id,
                'nombre' => $oficina->nombre,
                'estado_id' => $oficina->estado_id,
                'ciudad' => $municipio ? $municipio->nombre : null,
                'direccion' => $oficina->direccion,
                'telefono' => $oficina->telefono,
                'codigo_postal' => $codigoPostal ? $codigoPostal->codigo : null,
            ];
        }), 200);
    }

    public function opt_relacionadas($id) {

        $seg = Oficina::where('oficina_relacionada_id', $id)->get();

        if ($seg->isEmpty()) {
            $data = [
                'message' => 'Esta oficina no esta relacionada.',
                'status' => '404'               
            ];
            return response()->json($data, 404);
        }
        $data = [
                'estados' => $seg,
                'status' => '200',                
            ];

            return response()->json($data, 200);
    }

}
