<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datos = [
            ["nombre" => "SERVICIO POSTAL UNIVERSAL (Cartas e Impresos)", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "APARTADO POSTAL", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "TELEGRAMA", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "PORTA PAGADO", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "PORTA A PAGAR", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "TARJETA POSTAL", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "SERVICIOS ESPECIALES REGIMEN NACIONAL", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "RECOLECCION A DOMICILIO", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "SERVICIO ENVIOS EXPRESOS BOLIVARIANOS", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "IposPlus", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "Almacenamiento", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "Imprenta", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "Filatelia", "activo" => true, "created_at" => now(), "nacional" => true],

            ["nombre" => "SERVICIO POSTAL UNIVERSAL INTERNACIONAL", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "PEQUEÑOS PAQUETES", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "TARJETA POSTAL INTERNACIONAL", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "CECOGRAMA (ENVIOS PARA CIEGOS)", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "SACAS M", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "ENCOMIENDAS INTERNACIONALES VIA AEREA", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "SERVICIOS ESPECIALES REGIMEN NACIONAL (INTERNACIONAL)", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "SERVICIO EXPRESO REGIMEN INTERNACIONAL ESPECIAL EXPRESS MAIL SERVICE", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "EXPORTA FÁCIL POSTAL", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "BULTO POSTAL", "activo" => true, "created_at" => now(), "nacional" => false],
            ["nombre" => "CLIENTE CORPORATIVO", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "CARTERIA OFICIAL", "activo" => true, "created_at" => now(), "nacional" => true],
            ["nombre" => "APOSTILLA", "activo" => true, "created_at" => now(), "nacional" => true],
        ];

        foreach ($datos as $dato) {
            Servicio::updateOrCreate(
                ['nombre' => $dato['nombre']],
                ['activo' => true, 'created_at' => now(), 'nacional' => $dato['nacional']]
            );
        }
    }
}
