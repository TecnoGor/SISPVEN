<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Envio;
use App\Models\EnvioAlmacen;
use App\Models\Saca;
use App\Models\EnvioSaca;
use Illuminate\Support\Str;
use App\Models\TipoSaca;
use App\Models\Oficina;


class SacasController extends Controller
{

    public function index ($id) {

        $query = Saca::where('oficina_id', $id)->where('cerrado', false);

        $seg = $query->get();
        
        if ($seg->isEmpty()) {
            $data = [
                'message' => 'Saca no encontrada',
                'status' => '404'               
            ];
            return response()->json($data, 404);
        }

        $data = [
            'saca' => $seg,
            'status' => '200',                
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'oficina_id' => 'required|exists:oficinas,oficina_id|integer',
            'tipo_saca_id' => 'required|exists:tipos_sacas,tipo_saca_id|integer',
            'usuario_id' => 'required|exists:users,id|integer',
            'oficina_destino_id' => 'required|exists:oficinas,oficina_id|integer',
        ]);
        
        if($validator->fails()) {
            $data = [
                'message' => 'Error al validar datos',
                'error' => $validator->errors()->all(),
                'status' => '400',                
            ];
            return response()->json($data, 400);
        }

        $aleatorio = Str::upper(Str::random(8));
        $oficinaNombre = substr(Oficina::where('oficina_id', $request->oficina_id)->value('nombre'), 0, 9); 
        do {
            $aleatorio = Str::upper(Str::random(6));
            $codigo = $oficinaNombre.'-'.$aleatorio.'-'.date('Y');
            $nombreCodigo = $oficinaNombre.'-'.$aleatorio.'-'.date('Y');
        } while (Saca::where('codigo_saca', $codigo)->exists());


        $query = Saca::create([

            'oficina_id' => $request->oficina_id,
            'tipo_saca_id' => $request->tipo_saca_id,
            'usuario_id' => $request->usuario_id,
            'oficina_destino_id' => $request->oficina_destino_id,
            'codigo_saca' => $codigo,
            'cerrado' => false,

        ]);

        if (!$query) {
            $data = [
                'message' => 'Error al crear saca',
                'status' => '500',                
            ];
            return response()->json($data, 500);
        }
        $data = [
            'name' => $query,
            'status' => '200',                
        ];
        return response()->json($data, 200);
    }

    public function link(Request $request) {

        $validator = Validator::make($request->all(), [
            'envio_id' => 'required|array', // Cambiar a 'array' si se espera un array de IDs
            'envio_id.*' => 'required|exists:envios,codigo_envio', // Validar cada ID en el array
            'saca_id' => 'required|exists:sacas,codigo_saca'

        ]);
        
        if($validator->fails()) {
            $data = [
                'message' => 'Error al validar datos',
                'error' => $validator->errors()->all(),
                'status' => '400',                
            ];
            return response()->json($data, 400);
        }

        $envios = $request->envio_id;
        $saca_id = Saca::where('codigo_saca', $request->saca_id)->select('saca_id')->first();
        $saca = $saca_id->saca_id;
        $i = 0;

        foreach ($envios as $envio) {

            $envio_query = Envio::where('codigo_envio', $envio)->select('envio_id')->first();
            $envio_id = $envio_query->envio_id;

            $exists = EnvioSaca::where('saca_id', $saca)
            ->where('envio_id', $envio_id)
            ->exists();

            $active = EnvioSaca::where('envio_id', $envio_id)
            ->where('activo', true)
            ->exists();

            if ($exists) {
                $data = [
                    'message' => 'El envio '. $envio .' ya se encuentra asociado a esta saca '. $request->saca_id,
                    'status' => '500',                
                ];
                return response()->json($data, 500);
            }

            if ($active) {

                $data = [
                    'message' => 'El envio '. $envio .' ya se encuentra en otra saca activa.',
                    'status' => '400',
                ];
                return response()->json($data, 400);

            }

            if (!$exists) {

            $query = EnvioSaca::create([

                'saca_id' => $saca,
                'envio_id' => $envio_id,
                'activo' => true,

            ]);

            if (!$query) {
                $data = [
                    'message' => 'Error al crear saca',
                    'status' => '500',                
                ];
                return response()->json($data, 500);
            }


            $segAlmacen = EnvioAlmacen::where('envio_id', $envio_id)
            ->latest()->first();

                $segAlmacen->saca_id = $saca;

                $segAlmacen->save();

            }

            $i++;
        }

        if ($exists) {
            $data = [
                'message' => 'El envío '. $envio_id .'ya pertenece a la saca activa '. $saca,
                'status' => '500',                
            ];
            return response()->json($data, 500);
        }

        $data = [
            'name' => $query,
            'status' => '200',                
        ];
        return response()->json($data, 200);
        
    }

    public function getEnviosBySaca($id)
    {

        $saca_query = Saca::where('codigo_saca', $id)->first();

        $saca = $saca_query->saca_id;

        $query = EnvioSaca::where('saca_id', $saca)->where('activo', true);

        $seg = $query->get();
        
        if ($seg->isEmpty()) {
            $data = [
                'message' => 'No se han encontrado resultados',
                'status' => '404'               
            ];
            return response()->json($data, 404);
        }

        $data = [
            'saca' => $seg,
            'status' => '200',                
        ];

        return response()->json($data, 200);
    }

    public function codesaca($code) {

        $seg = Saca::where('codigo_saca', $code)->first();
        
        if (!$seg) {
            $data = [
                'message' => 'Saca no encontrada',
                'status' => '404'               
            ];
            return response()->json($data, 404);
        }

        $data = [
            'saca' => $seg,
            'status' => '200',                
        ];

        return response()->json($data, 200);

    }

    public function tiposaca() {

        $seg = TipoSaca::all();
        
        if (!$seg) {
            $data = [
                'message' => 'Hubo un error en la base de datos',
                'status' => '404'               
            ];
            return response()->json($data, 500);
        }

        $data = [
            'tipo_sacas' => $seg,
            'status' => '200',                
        ];

        return response()->json($data, 200);

    }

}

