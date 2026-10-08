<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CambiarNombreServicioYSaca extends Seeder
{
    public function run(): void
    {
        $this->marcarServiciosEnvio();
        // $this->renombrarYDesactivarServicios();
    }

    private function marcarServiciosEnvio(): void
    {
        // IDs de servicios que SÍ son envíos (transportan correspondencia/paquetes).
        $serviciosEnvio = [
            1,   // SERVICIO POSTAL UNIVERSAL (Cartas e Impresos)
            6,   // TARJETA POSTAL
            7,   // SERVICIOS ESPECIALES REGIMEN NACIONAL
            9,   // SERVICIO ENVIOS EXPRESOS BOLIVARIANOS
            10,  // IposPlus
            14,  // SERVICIO POSTAL UNIVERSAL INTERNACIONAL
            15,  // PEQUEÑOS PAQUETES
            16,  // TARJETA POSTAL INTERNACIONAL
            17,  // CECOGRAMA (ENVIOS PARA CIEGOS)
            18,  // SACAS M
            19,  // ENCOMIENDAS INTERNACIONALES VIA AEREA
            20,  // SERVICIOS ESPECIALES REGIMEN NACIONAL (INTERNACIONAL)
            21,  // SERVICIO EXPRESO REGIMEN INTERNACIONAL ESPECIAL EXPRESS MAIL SERVICE
            22,  // EXPORTA FÁCIL POSTAL
            23,  // BULTO POSTAL
            24,  // CLIENTE CORPORATIVO
            25,  // CARTERIA OFICIAL
        ];

        // Marcar los que son envíos.
        DB::table('servicios')
            ->whereIn('servicio_id', $serviciosEnvio)
            ->update(['es_envio' => true]);

        // Asegurar el resto en false (idempotente, ejecutable varias veces sin desviar el estado).
        DB::table('servicios')
            ->whereNotIn('servicio_id', $serviciosEnvio)
            ->update(['es_envio' => false]);
    }


    // private function renombrarYDesactivarServicios(): void
    // {
    //     // Servicio 1: pasa a ser "CARTAS" (cubre nacional + internacional ordinario).
    //     DB::table('servicios')
    //         ->where('servicio_id', 1)
    //         ->update(['nombre' => 'CARTAS ORDINARIAS']);

    //     // Servicio 14: pasa a ser "CARTAS CERTIFICADAS Y PEQUEÑOS PAQUETES"
    //     // (cubre nacional + internacional certificado y los antiguos Pequeños Paquetes).
    //     DB::table('servicios')
    //         ->where('servicio_id', 14)
    //         ->update(['nombre' => 'CARTAS CERTIFICADAS Y PEQUEÑOS PAQUETES']);

    //     // Servicio 15 (PEQUEÑOS PAQUETES): se fusiona con el 14, queda inactivo.
    //     DB::table('servicios')
    //         ->where('servicio_id', 15)
    //         ->update(['activo' => false, 'es_envio' => false]);
    // }
}
