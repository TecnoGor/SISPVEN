<?php

namespace Database\Seeders;

use App\Models\EnvioEstatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EnviosEstatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EnvioEstatus::where('estatus', 'Entregado al Cartero')->update(['estatus' => 'Asignado a Repartidor']);

        $datos = [
            ["estatus" => "Envío en Proceso"],
            ["estatus" => "Envío Procesado"],
            ["estatus" => "Llegada desde OPT"],
            ["estatus" => "Llegada desde COP"],
            ["estatus" => "Entrada desde CPC"],
            ["estatus" => "Entrada desde CPI"],
            ["estatus" => "Entrada a Aduana"],
            ["estatus" => "Entrada desde Externo"],
            ["estatus" => "Salida hacia Externo"],
            ["estatus" => "Salida de Aduana"],
            ["estatus" => "Salida hacia CPI"],
            ["estatus" => "Salida hacia CPC"],
            ["estatus" => "Salida hacia COP"],
            ["estatus" => "Salida hacia OPT"],
            ["estatus" => "Disponible en Agencia"],
            ["estatus" => "Asignado a Repartidor"],
            ["estatus" => "Entregado al Cliente"],
            ["estatus" => "Recibido por Repartidor"],
            ["estatus" => "SALIDA HACIA DISTRIBUCIÓN PAQUETES MUESTRAS"],
            ["estatus" => "ENTRADA DISTRIBUCIÓN PAQUETES MUESTRAS"],
            ["estatus" => "SALIDA HACIA UNIDAD DE ANALISIS DE DEVOLUCION"],
            ["estatus" => "ENTRADA EN UNIDAD DE ANALISIS DE DEVOLUCION"],
            ["estatus" => "SALIDA HACIA EXPEDICION DE BULTO"],
            ["estatus" => "ENTRADA EN EXPEDICION DE BULTO"],
            ["estatus" => "SALIDA HACIA REZAGO"],
            ["estatus" => "ENTRADA EN REZAGO"],
            ["estatus" => "SALIDA HACIA ALMACEN BULTO POSTAL"],
            ["estatus" => "ENTRADA EN ALMACEN BULTO POSTAL"],
            ["estatus" => "SALIDA A EXPORTACION"],
            ["estatus" => "ENTRADA EN EXPORTACION"],
            ["estatus" => "ENTRADA HACIA ALMACEN EMS"],
            ["estatus" => "ENTRADA EN ALMACEN EMS"],
            ["estatus" => "SALIDA HACIA DISTRIBUCION"],
            ["estatus" => "ENTRADA EN DISTRIBUCION"],
            ["estatus" => "SALIDA HACIA EXPEDICION EMS"],
            ["estatus" => "ENTRADA EN EXPEDICION EMS"],
            ["estatus" => "ENTRADA EN APERTURA"],
            ["estatus" => "SALIDA HACIA APERTURA"],
            ["estatus" => "SALIDA HACIA EXPEDICION IPOSPLUS"],
            ["estatus" => "ENTRADA EN EXPEDICION IPOSPLUS"],
            ["estatus" => "SALIDA HACIA EXPEDICION"],
            ["estatus" => "ENTRADA EN EXPEDICION"],
            ["estatus" => "ENVÍO EN DEVOLUCIÓN"],


        ];

        foreach ($datos as $dato) {
            EnvioEstatus::firstOrCreate(
                ['estatus' => $dato['estatus']],
                ['created_at' => now()]
            );
        }
    }
}
