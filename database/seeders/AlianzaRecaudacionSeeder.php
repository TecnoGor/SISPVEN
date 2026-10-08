<?php

namespace Database\Seeders;

use App\Models\TipoAlianza;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EnteAliadoRecaudacion;
use App\Models\CatalogoServicioAlianza;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AlianzaRecaudacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entesAliados = [ 
            ['nombre' => 'Cancilleria', 'activo' => true], 
            ['nombre' => 'Alcaldia', 'activo' => true],
            ['nombre' => 'Socio Comercial', 'activo' => true], 
        ]; 

        $tiposAlianzas = [ 
            ['nombre' => 'Recaudacion con Comision', 'activo' => true], 
            ['nombre' => 'Consignacion y Tramite', 'activo' => true],
            ['nombre' => 'Venta de Producto', 'activo' => true],  
        ]; 

        $catalogoServicios = [ 
            ['nombre' => 'Apostilla/Legalizacion', 'activo' => true], 
            ['nombre' => 'Pago de Impuesto', 'activo' => true], 
        ]; 

        foreach ($entesAliados as $ente) { 
            EnteAliadoRecaudacion::firstOrCreate( 
                ['nombre' => $ente['nombre']],  
                ['activo' => $ente['activo']] 
            ); 
        } 

        foreach ($tiposAlianzas as $tipo) { 
            TipoAlianza::firstOrCreate( 
                ['nombre' => $tipo['nombre']], 
                ['activo' => $tipo['activo']] 
            ); 
        }  

        foreach ($catalogoServicios as $servicio) { 
            CatalogoServicioAlianza::firstOrCreate( 
                ['nombre' => $servicio['nombre']], 
                ['activo' => $servicio['activo']] 
            ); 
        }
    }
}
