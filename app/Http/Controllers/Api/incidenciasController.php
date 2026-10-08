<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incidencia;
use Illuminate\Support\Facades\Validator;
use  App\Models\IncidenciaDetalle;
use App\Models\EnvioIncidencia;
use App\Models\Envio;

class incidenciasController extends Controller
{
    public function incidencias (){
        $get = Incidencia::all();
        if($get->isEmpty()){
            $data = [
                'message'=> 'No hay datos',
                'status' => '404',
            ];                                          
            return response()->json($data, 404);
        }    
        return response()->json($get, 200);
    }
    
    public function registerincidencia(Request $request){
        $validator = Validator::make($request->all(),[
            'envio_codigo' => 'required|exists:envios,codigo_envio',
            'incidencia_id' => 'required|array',
            'incidencia_id.*' => 'required|exists:incidencias,incidencia_id',
            'usuario_id' => 'required|exists:users,id',
            'oficina_id' => 'required|exists:oficinas,oficina_id',
            'detalle' => 'required|string',
        ]);
        if($validator->fails()){
            $data = [
                'message'=>'Error al validar datos',
                'error'=> $validator->errors()->all(),
                'status' => '400',
            ];
            return response()->json($data, 400);
        }
        $reQ = $request->envio_codigo;

        $seg = Envio::where('codigo_envio', $reQ)
        ->first();
    
         $regis = EnvioIncidencia::create([
            'envio_id'=>  $seg->envio_id,	
            'detalle' => $request->detalle,
            'usuario_id'=> $request->usuario_id,
            'oficina_id'=> $request->oficina_id,
        ]); 
        foreach($request->incidencia_id as $incidencia_id){
            $regis2 = IncidenciaDetalle::create([
                'envio_incidencia_id'=>  $regis->envio_incidencia_id,	
                'incidencia_id' => $incidencia_id,
            ]);
        }
        $message = [
            'message' => 'Incidencia registrada',
            'status' => '200',
        ];
        return response()->json($message, 200);
    }
    
}
