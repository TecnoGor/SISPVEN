<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class EnviosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('envios')->insert([


            // ["servicio_id" => 1, "tipo_envio" => "nacional", "oficina_id" => 78, "usuario_id" => 4, "nombre_rem" => "Carlos", "apellido_rem" => "Lopez",
            // "tipo_documento_rem" => "V", "documento_rem" => 23456789, "codigo_postal_rem" => 1002, "estado_rem" => 2, "municipio_rem" => 2,
            // "parroquia_rem" => 23, 'ciudad_rem' => 1, "direccion_rem" => "Calle 12, No. 30", "correo_rem" => "carloslopez@example.com",
            // "telefono_rem" => 4161234567, "nombre_dest" => "Ana", "apellido_dest" => "Torres", "tipo_documento_dest" => "V",
            // "documento_dest" => 123456789, "codigo_postal_dest" => 2002, "continente_dest" => null, "pais_dest" => null,
            // "estado_dest" => 2, "municipio_dest" => 2, "parroquia_dest" => 23, 'ciudad_dest' => 2,
            // "direccion_dest" => "Av. Sucre, No. 15", "tlf_dest" => "04234567890", "correo_dest" => "anatorres@example.com",
            // "servicio_expreso" => null, "peso" => "700", "coste" => "1000",
            // "apartado_postal" => null, "codigo_envio" => 'OP011CP005000000001', "contenido" => "Documentos", "devolucion" => false, "descubierto" => false,
            // "tipo_saca_id" => 1, "created_at" => Carbon::create(2025, 2, 17, 0, 0, 0)],

            // ["servicio_id" => 1, "tipo_envio" => "nacional", "oficina_id" => 78, "usuario_id" => 4, "nombre_rem" => "Carlos", "apellido_rem" => "Lopez",
            // "tipo_documento_rem" => "V", "documento_rem" => 23456789, "codigo_postal_rem" => 1002, "estado_rem" => 2, "municipio_rem" => 2,
            // "parroquia_rem" => 23, 'ciudad_rem' => 1, "direccion_rem" => "Calle 12, No. 30", "correo_rem" => "carloslopez@example.com",
            // "telefono_rem" => 4161234567, "nombre_dest" => "Ana", "apellido_dest" => "Torres", "tipo_documento_dest" => "V",
            // "documento_dest" => 123456789, "codigo_postal_dest" => 2002, "continente_dest" => null, "pais_dest" => null,
            // "estado_dest" => 2, "municipio_dest" => 2, "parroquia_dest" => 23, 'ciudad_dest' => 2,
            // "direccion_dest" => "Av. Sucre, No. 15", "tlf_dest" => "04234567890", "correo_dest" => "anatorres@example.com",
            // "servicio_expreso" => null, "peso" => "700", "coste" => "1000",
            // "apartado_postal" => null, "codigo_envio" => 'OP011CP005000000002', "contenido" => "Documentos", "devolucion" => false, "descubierto" => false,
            // "tipo_saca_id" => 1, "created_at" => Carbon::create(2025, 2, 17, 0, 0, 0)],

        ]);
    }
}
