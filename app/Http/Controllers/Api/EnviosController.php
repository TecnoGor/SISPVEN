<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Envio;
use App\Models\EnvioSaca;
use App\Models\Saca;
use App\Models\EnvioAlmacen;
use Illuminate\Support\Facades\Validator;

class EnviosController extends Controller
{
    public function index () {
        $seg = Envio::all();
        
        if ($seg->isEmpty()) {
            $data = [
                'message' => 'No hay datos',
                'status' => '200',                
            ];
            return response()->json($data, 200);
        }

        return response()->json($seg, 200);
    }

    public function show($id) {

        $seg = Envio::where('codigo_envio', $id)
        ->first();

        if (!$seg) {

            $saca = Saca::where('codigo_saca', $id)
            ->first();

            if (!$saca)
            {
                $data = [
                    'message' => 'Este codigo no es valido.',
                    'status' => '404',
                ];

                return response()->json($data, 404);
            }

            $envios = EnvioSaca::where('saca_id', $saca->saca_id)
            ->get();

            if ($envios->isEmpty())
            {
                $data = [
                    'message' => 'Esta saca no tiene envíos.',
                    'status' => '404',
                ];
                    return response()->json($data, 404);
            }

            foreach ($envios as $envio) {
                $query_envio = Envio::find($envio->envio_id);

                $code_envio[] = $query_envio->codigo_envio;
            }
            
            $data = [
                'envio' => [
                    'codigo_envio' => $saca->codigo_saca
                ],
                'status' => '200',
            ];

            return response()->json($data, 200);
        }

        $data = [
            'envio' => $seg,
            'status' => '200',                
        ];

        return response()->json($data, 200);
    }

    public function getEnviosInAlmacen ($id) {

        $datos = [
            'oficina_id' => $id,
        ];

        $validator = Validator::make($datos, [
            'oficina_id' => 'required|exists:oficinas,oficina_id|integer',
        ]);
        
        if($validator->fails()) {
            $data = [
                'message' => 'Error al validar datos',
                'error' => $validator->errors()->all(),
                'status' => '400',                
            ];
            return response()->json($data, 400);
        }

        $query = EnvioAlmacen::where('oficina_id', $id)->where('estatus', true)->get();


        foreach ($query as $key => $envio) {

            if (isset($envio->saca_id)) {

                $confirmar_cierre = Saca::find($envio->saca_id);

                if ($confirmar_cierre->cerrado) {
                    $envio->codigo_saca = $confirmar_cierre->codigo_saca;
                }
                else {
                    $query->forget($key);
                }

            }

        }

        if ($query->isEmpty()) {
            $data = [
                'message' => 'Esta oficina no tiene envíos en su almacén.',
                'status' => '404'               
            ];
            return response()->json($data, 404);
        }

        $data = [
            'envios' => $query,
            'status' => '200',                
        ];

        return response()->json($data, 200);
    }

}
