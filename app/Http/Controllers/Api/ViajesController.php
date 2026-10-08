<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RutaPuntoEntrega;
use App\Models\Rutas;
use App\Models\Viaje;
use App\Models\Oficina;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ViajesController extends Controller
{

    public function show($oficina, $fecha) {

        $i = 0;
        $viajeIds = []; // Array para almacenar los viaje_id únicos
        $viaje_data = [];
    
        $seg = RutaPuntoEntrega::where('oficina_id', $oficina)->get();
        $route = Rutas::where('oficina_id_origen', $oficina)->get();
        $route_dest = Rutas::where('oficina_id_destino', $oficina)->get();

        $fechaCarbon = Carbon::parse($fecha);
        $inicioSemana = $fechaCarbon->startOfWeek()->format('Y-m-d');


        if ($seg->isNotEmpty()) {
            foreach ($seg as $segui) {
                $viajes = Viaje::where('ruta_id', $segui->ruta_id)
                          ->where('fecha_salida', $inicioSemana)
                          ->get();
                
                if ($viajes->isNotEmpty()) {
                    foreach ($viajes as $viaje) {
                        if (!in_array($viaje->viaje_id, $viajeIds)) {
                            $viaje_data[$i] = $viaje;
                            $viajeIds[] = $viaje->viaje_id; 
                            $i++;
                        }
                    }
                }
            }
        }

        if ($route->isNotEmpty()) {
            foreach ($route as $segui) {
                
                $viajes = Viaje::where('ruta_id', $segui->ruta_id)
                ->where('fecha_salida', $inicioSemana)
                ->get();

                if ($viajes->isNotEmpty()) {
                    foreach ($viajes as $viaje) {
                        if (!in_array($viaje->viaje_id, $viajeIds)) {
                            $viaje_data[$i] = $viaje;
                            $viajeIds[] = $viaje->viaje_id;
                            $i++;

                        }
                    }
                }
            }
        }   

        if ($route_dest->isNotEmpty()) {
            foreach ($route_dest as $segui) {
                
                $viajes = Viaje::where('ruta_id', $segui->ruta_id)
                ->where('fecha_salida', $inicioSemana)
                ->get();

                if ($viajes->isNotEmpty()) {
                    foreach ($viajes as $viaje) {
                        if (!in_array($viaje->viaje_id, $viajeIds)) {
                            $viaje_data[$i] = $viaje;
                            $viajeIds[] = $viaje->viaje_id;
                            $i++;

                        }
                    }
                }
            }
        }   

        if (empty($viaje_data)) {
            $data = [
                'message' => 'No se encontro ningun viaje asociado a tu oficina.',
                'status' => '404'               
            ];
            return response()->json($data, 404);
        }

        foreach ($viaje_data as $data) {

            $destino = RutaPuntoEntrega::where('ruta_id', $data->ruta_id)->select('oficina_id')->get();

            $rutas = Rutas::find($data->ruta_id);

            if($rutas->oficina_id_destino == $oficina){

                $destinos[] = [
                    'paradas' => Oficina::find($destino),
                    'destino' => Oficina::find($rutas->oficina_id_origen),
                ];

            }
            else {
                       
            $destinos[] = [
                'paradas' => Oficina::find($destino),
                'destino' => Oficina::find($rutas->oficina_id_destino),
            ];

            }

        }

        $tipo_oficina = Oficina::find($oficina);

        if ($tipo_oficina->tipo_oficina_id == 1 || $tipo_oficina->tipo_oficina_id == 2 || $tipo_oficina->tipo_oficina_id == 3) {
            $find_cop = Oficina::where('tipo_oficina_id', 4)
            ->where('estado_id', $tipo_oficina->estado_id)
            ->first();

            $auxiliar_intervalo = 0;

            foreach ($destinos as $dest) {

                $dest[$auxiliar_intervalo]['destino'] = $find_cop;
                $dest[$auxiliar_intervalo]['paradas'] = null;

                $auxiliar_intervalo++;
            }

        }

        $data = [
                'viajes' => $viaje_data,
                'destinos' => $destinos,
                'status' => '200',                
            ];

            return response()->json($data, 200);
    }

    public function getViajeByCode ($codigo) {

        $seg = Viaje::where('codigo', $codigo)
        ->first();
        
        if (!$seg) {
            $data = [ 
                'message' => 'No se encontro ningun viaje con este codigo.',
                'status' => '404'
                ];
            return response()->json($data, 404);
        }

        $data = [ 
            'message' => $seg,
            'status' => '200'
            ];
            return response()->json($data, 200);
        
    }

}
