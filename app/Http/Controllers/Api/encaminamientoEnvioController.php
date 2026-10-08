<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnvioEncaminamiento;
use App\Models\Manifiesto;
use App\Models\ManifiestoPaquete;
use App\Models\Envio;
use App\Models\TipoSaca;
use App\Models\Saca;
use App\Models\EnvioSaca;
use App\Models\EnvioAlmacen;
use App\Models\Oficina;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class encaminamientoEnvioController extends Controller
{
    
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'envio_id' => 'required|array',
            'oficina_id' => 'required|exists:oficinas,oficina_id|integer',
            'oficina_externa_id' => 'exists:oficinas,oficina_id|integer|nullable',
            'usuario_id' => 'required|exists:users,id|integer',
            'tipe' => 'required|integer|in:1,2,3'
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

        $envios2 = $request->envio_id;

        $intervalo_envios = 0;

        foreach ($envios as $envio) {
            
            $saca_query = Saca::where('codigo_saca', $envio)->first();

            if ($saca_query) {
                
                $envios_saca = EnvioSaca::where('saca_id', $saca_query->saca_id)
                ->where('activo', true)
                ->get();

                foreach($envios_saca as $envio_saca) {

                    $envio_code = Envio::find($envio_saca->envio_id);

                    $envios[] = $envio_code->codigo_envio;

                    if($request->tipe == 1)
                    {
 
                        $envio_saca->activo = false;
                        $envio_saca->save();

                    }

                }


                unset($envios[$intervalo_envios]);

            }



            $intervalo_envios++;

        }
        
        $envios = array_values($envios);

        $validator = Validator::make($envios, [
            'envio_id.*' => 'required|exists:envios,codigo_envio'
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error al validar datos',
                'error' => $validator->errors()->all(),
                'status' => '400',                
            ];
            return response()->json($data, 400);
        }

        $i = 0;        

        if($request->tipe == 3){
            
            $envio_query = Envio::where('codigo_envio', $request->envio_id)->select('envio_id')->first();
            $envio_id = $envio_query->envio_id;
            $viaje_id = EnvioEncaminamiento::where('envio_id', $envio_id)->whereNotIn('estatus_id', [1, 2])->latest()->first()->viaje_id ?? null;
            $envio_entrega = EnvioEncaminamiento::where('envio_id', $envio_id)->latest()->first();
            $estatus = 17;
        }

        if($request->tipe == 2){

            $tipo_oficina = Oficina::find($request->oficina_externa_id);

            if ($tipo_oficina->tipo_oficina_id == 1 || $tipo_oficina->tipo_oficina_id == 2 || $tipo_oficina->tipo_oficina_id == 3) {
                $estatus = 14;
            }
            if ($tipo_oficina->tipo_oficina_id == 4) {
                $estatus = 13;
            }
            if ($tipo_oficina->tipo_oficina_id == 5) {
                $estatus = 12;
            }
            if ($tipo_oficina->tipo_oficina_id == 6) {
                $estatus = 11;
            }
            if ($tipo_oficina->tipo_oficina_id == 7) {
                $estatus = 10;
            }
        }

        foreach ($envios as $envio) {

            $envio_query = Envio::where('codigo_envio', $envio)->select('envio_id')->first();
            $envio_id = $envio_query->envio_id;

            $devolucion = EnvioEncaminamiento::where('envio_id', $envio_id)
            ->where('estatus_id', $request->estatus_id)
            ->where('oficina_id', $request->oficina_id)
            ->where('devolucion', true)
            ->exists();

            if($request->tipe == 1){

                $envio_entrada = EnvioEncaminamiento::where('envio_id', $envio_id)->latest()->first();
                
                $viaje_id = EnvioEncaminamiento::where('envio_id', $envio_id)->whereNotIn('estatus_id', [1, 2])->latest()->first()->viaje_id ?? null;

                $tipo_oficina = Oficina::find($envio_entrada->oficina_id);

                if ($tipo_oficina->tipo_oficina_id == 1 || $tipo_oficina->tipo_oficina_id == 2 || $tipo_oficina->tipo_oficina_id == 3) {
                    $estatus = 3;
                }
                if ($tipo_oficina->tipo_oficina_id == 4) {
                    $estatus = 4;
                }
                if ($tipo_oficina->tipo_oficina_id == 5) {
                    $estatus = 5;
                }
                if ($tipo_oficina->tipo_oficina_id == 6) {
                    $estatus = 6;
                }
                if ($tipo_oficina->tipo_oficina_id == 7) {
                    $estatus = 7;
                }

            }

            $exists = EnvioEncaminamiento::where('envio_id', $envio_id)
            ->where('estatus_id', $estatus)
            ->where('oficina_id', $request->oficina_id)
            ->where('devolucion', false)
            ->first();

            if ($exists) {
                $message[] = 'El registro de '.$envio.' ya fue realizado, no se puede repetir.';
                continue;
            }

            if ($request->tipe == 1) {
                $almacen = EnvioAlmacen::create([
                    'envio_id' => $envio_id,
                    'oficina_id' => $request->oficina_id,
                    'codigo' => $envio,
                    'saca_id' => null,
                    'estatus' => true,
                ]);
            }
            if ($request->tipe == 2) 
            {

                $seg = EnvioAlmacen::where('envio_id', $envio_id)
                ->where('oficina_id', $request->oficina_id)
                ->latest()->first();

                $seg->estatus = false;

                $seg->save();
                
                $viaje_id = $request->viaje_id;
            }

            if ($devolucion) {

                $segui = EnvioEncaminamiento::create([
                    'envio_id' => $envio_id,
                    'oficina_id' => $request->oficina_id,
                    'oficina_externa_id' => $request->oficina_externa_id,
                    'usuario_id' => $request->usuario_id,
                    'viaje_id' => $viaje_id,
                    'estatus_id' => $estatus,
                    'devolucion' => true
                ]);

            }
            else 
            {
            
        $segui = EnvioEncaminamiento::create([
            'envio_id' => $envio_id,
            'oficina_id' => $request->oficina_id,
            'oficina_externa_id' => $request->oficina_externa_id,
            'usuario_id' => $request->usuario_id,
            'viaje_id' => $viaje_id,
            'estatus_id' => $estatus,
            'devolucion' => false,
        ]);
            
    }

        if (!$segui) {
            $data = [
                'message' => 'Hubo un error al crear el encaminamiento del envio '.$envio_id,
                'status' => '500',                
            ];
            return response()->json($data, 500);
        }

        }

        if (!isset($segui) && $message) {
            $data = [
                'warning' => 'Todos los envios ya fueron registrados previamente.',
                'message' => $message,
                'status' => '200',                
            ];
            return response()->json($data, 200);
        }
        if ($segui && !isset($message)) {

            if ($request->tipe == 2) {
                
            foreach($envios2 as $envio) {

                $sacas_query = Saca::where('codigo_saca', $envio)->first();

                if (!$sacas_query) {

                    $envios_query = Envio::where('codigo_envio', $envio)->first();

                    if ($envios_query) {

                        $confirmar_manifiesto = Manifiesto::where('status', false)->where('oficina_id', $request->oficina_id)
                        ->where('oficina_destino_id', $request->oficina_externa_id)->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                        ->first();
    
                        if ($confirmar_manifiesto) {
                            $manifiesto_paquetes = ManifiestoPaquete::create([
                                'manifiesto_id' => $confirmar_manifiesto->manifiesto_id,
                                'saca_id' => null,
                                'envio_id' => $envios_query->envio_id,
                                'servicio_id' => $envios_query->servicio_id,
                                'peso' => $envios_query->peso,
                            ]);
                        }
                        else {
                            $manifiesto = Manifiesto::create([
                                'oficina_id' => $request->oficina_id,
                                'oficina_destino_id' => $request->oficina_externa_id,
                                'status' => false,
                            ]);
    
                            $manifiesto_paquetes = ManifiestoPaquete::create([
                                'manifiesto_id' => $manifiesto->manifiesto_id,
                                'saca_id' => null,
                                'envio_id' => $envios_query->envio_id,
                                'servicio_id' => $envios_query->servicio_id,
                                'peso' => $envios_query->peso,
                            ]);
                        }
                    }
    
                }
                if ($sacas_query) {

                    $sacas_query->servicio_id = TipoSaca::find($sacas_query->tipo_saca_id)->servicio_id;

                    $confirmar_manifiesto = Manifiesto::where('status', false)->where('oficina_id', $request->oficina_id)
                    ->where('oficina_destino_id', $sacas_query->oficina_destino_id)->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                    ->first();

                    if ($confirmar_manifiesto) {
                        $manifiesto_paquetes = ManifiestoPaquete::create([
                            'manifiesto_id' => $confirmar_manifiesto->manifiesto_id,
                            'saca_id' => $sacas_query->saca_id,
                            'envio_id' => null,
                            'servicio_id' => $sacas_query->servicio_id,
                            'peso' => $sacas_query->peso,
                        ]);
                    }
                    else {
                        $manifiesto = Manifiesto::create([
                            'oficina_id' => $request->oficina_id,
                            'oficina_destino_id' => $sacas_query->oficina_destino_id,
                            'status' => false,
                        ]);

                        $manifiesto_paquetes = ManifiestoPaquete::create([
                            'manifiesto_id' => $manifiesto->manifiesto_id,
                            'saca_id' => $sacas_query->saca_id,
                            'envio_id' => null,
                            'servicio_id' => $sacas_query->servicio_id,
                            'peso' => $sacas_query->peso,
                        ]);
                    }
                }
            }
            }

            $data = [
                'name' => $segui,
                'status' => '201',                
            ];
            return response()->json($data, 200);
        }
        
        if (!$segui) {
            $data = [
                'message' => 'Error al crear encaminamiento',
                'status' => '500',                
            ];
            return response()->json($data, 500);
        }

        if ($request->tipe == 2) {
                
            foreach($request->envio_id as $envio) {

                $sacas_query = Saca::where('codigo_saca', $envio)->first();

                if (!$sacas_query) {

                    $envios_query = Envio::where('codigo_envio', $envio)->first();

                    if ($envios_query) {

                        $confirmar_manifiesto = Manifiesto::where('status', false)->where('oficina_id', $request->oficina_id)
                        ->where('oficina_destino_id', $request->oficina_destino_id)->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                        ->first();
    
                        if ($confirmar_manifiesto) {
                            $manifiesto_paquetes = ManifiestoPaquete::create([
                                'manifiesto_id' => $confirmar_manifiesto->manifiesto_id,
                                'saca_id' => null,
                                'envio_id' => $envios_query->envio_id,
                                'servicio_id' => $envios_query->servicio_id,
                                'peso' => $envios_query->peso,
                            ]);
                        }
                        else {
                            $manifiesto = Manifiesto::create([
                                'oficina_id' => $request->oficina_id,
                                'oficina_destino_id' => $request->oficina_destino_id,
                                'status' => false,
                            ]);
    
                            $manifiesto_paquetes = ManifiestoPaquete::create([
                                'manifiesto_id' => $manifiesto->manifiesto_id,
                                'saca_id' => null,
                                'envio_id' => $envios_query->envio_id,
                                'servicio_id' => $envios_query->servicio_id,
                                'peso' => $envios_query->peso,
                            ]);
                        }
                    }
    
                }
                if ($sacas_query) {

                    $sacas_query->servicio_id = TipoSaca::find($sacas_query->tipo_saca_id)->servicio_id;

                    $confirmar_manifiesto = Manifiesto::where('status', false)->where('oficina_id', $request->oficina_id)
                    ->where('oficina_destino_id', $sacas_query->oficina_destino_id)->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                    ->first();

                    if ($confirmar_manifiesto) {
                        $manifiesto_paquetes = ManifiestoPaquete::create([
                            'manifiesto_id' => $confirmar_manifiesto->manifiesto_id,
                            'saca_id' => $sacas_query->saca_id,
                            'envio_id' => null,
                            'servicio_id' => $sacas_query->servicio_id,
                            'peso' => $sacas_query->peso,
                        ]);
                    }
                    else {
                        $manifiesto = Manifiesto::create([
                            'oficina_id' => $request->oficina_id,
                            'oficina_destino_id' => $sacas_query->oficina_destino_id,
                            'status' => false,
                        ]);

                        $manifiesto_paquetes = ManifiestoPaquete::create([
                            'manifiesto_id' => $manifiesto->manifiesto_id,
                            'saca_id' => $sacas_query->saca_id,
                            'envio_id' => null,
                            'servicio_id' => $sacas_query->servicio_id,
                            'peso' => $sacas_query->peso,
                        ]);
                    }
                }
            }
            }

            $data = [
                'name' => $segui,
                'message' => $message,
                'status' => '201',                
            ];
            return response()->json($data, 200);
    }

    public function showEncaminamientoCliente(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_envio' => 'required|exists:envios,codigo_envio',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al validar datos',
                'error' => $validator->errors()->all(),
                'status' => '400',
            ], 400);
        }
        $envio = Envio::where('codigo_envio', $request->codigo_envio)->first();
        if (!$envio) {
            return response()->json([
                'message' => 'Envío no encontrado',
                'status' => '404',
            ], 404);
        }
        $encaminamientos = EnvioEncaminamiento::where('envio_id', $envio->envio_id)
            ->with(['oficinas:oficina_id,nombre', 'users:id,name', 'envio_estatus:envios_estatus_id,estatus'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'fecha' => $item->created_at,
                    'oficina' => $item->oficinas ? $item->oficinas->nombre : null,
                    'oficina_externa' => $item->oficina_externa_id ? (Oficina::find($item->oficina_externa_id)->nombre ?? null) : null,
                    'usuario' => $item->users ? $item->users->name : null,
                    'estatus' => $item->envio_estatus ? $item->envio_estatus->estatus : null,
                    'devolucion' => $item->devolucion,
                ];
            });
        return response()->json([
            'codigo_envio' => $envio->codigo_envio,
            'encaminamientos' => $encaminamientos,
            'status' => '200',
        ], 200);
    }
}
