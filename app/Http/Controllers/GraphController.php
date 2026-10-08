<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Envio;
use App\Models\Oficina;
use App\Models\Servicio;
use App\Models\Viaje;
use App\Models\OficinaSemaforoPostal;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class GraphController extends Controller
{


    public function totalOficinas () {

        $result = [];

        $result['data'][0] = Oficina::where('externa', false)->where('estatus_id', 1)->count();
        $result['data'][1] = Oficina::where('externa', false)->where('estatus_id', 2)->count();
        $result['data'][2] = Oficina::where('externa', false)->where('estatus_id', 3)->count();

        $result['labels'][0] = 'Activas';
        $result['labels'][1] = 'Inoperativas';
        $result['labels'][2] = 'Inactivas';

        // return response()->json($result);
        
        return $result;

    }
    //cambios
    public function enviodiarosmes (){
        $result = [];

        $envios = Envio::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->count();
        $resuy['data'][0] = $envios;
        return $result;
    }


 public function tipeOficina (){
    $result = [];

    $oficinasArrendadas = OficinaSemaforoPostal::where('condicion', OficinaSemaforoPostal::CONDICION_ARRENDADA)->count();
    $oficinasIpostel = OficinaSemaforoPostal::where('condicion', OficinaSemaforoPostal::CONDICION_PROPIA_IPOSTEL)->count();
    $oficinasAcomodato = OficinaSemaforoPostal::where('condicion', OficinaSemaforoPostal::CONDICION_ENACOMODATO)->count();

    $result['data'][0] = $oficinasArrendadas;
    $result['data'][1] = $oficinasIpostel;
    $result['data'][2] = $oficinasAcomodato;
    $result['labels'][0] = OficinaSemaforoPostal::CONDICION_ARRENDADA;
    $result['labels'][1] = OficinaSemaforoPostal::CONDICION_PROPIA_IPOSTEL;
    $result['labels'][2] = OficinaSemaforoPostal::CONDICION_ENACOMODATO;
    return $result;

 }


}
