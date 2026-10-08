<?php

namespace Database\Seeders;

use App\Models\TipoSaca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposSacasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sacas = [
            ["nombre" => "Correo Ordinario", "nombre_referencial" => "Correo Ordinario", 'servicio_id' => 1, 'certificado' => false, "activo" => true, "created_at" => now()],
            ["nombre" => "Certificado", "nombre_referencial" => "Correo Ordinario Certificado", 'servicio_id' => 1, 'certificado' => true, "activo" => true, "created_at" => now()],
            ["nombre" => "Envios Expresos Bolivarianos", "nombre_referencial" => "Envios Expresos Bolivarianos", 'servicio_id' => 9, 'certificado' => true, "activo" => true, "created_at" => now()],
            ["nombre" => "Servicio Postal Universal Internacional", "nombre_referencial" => "Servicio Postal Universal Internacional", 'servicio_id' => 14, 'certificado' => false, "activo" => true, "created_at" => now()],
            ["nombre" => "Certificado", "nombre_referencial" => "Servicio Postal Universal Internacional Certificado", 'servicio_id' => 14, 'certificado' => true, "activo" => true, "created_at" => now()],
            ["nombre" => "Pequeños Paquetes", "nombre_referencial" => "Pequeños Paquetes", 'servicio_id' => 15, 'certificado' => false, "activo" => true, "created_at" => now()],
            ["nombre" => "Certificados", "nombre_referencial" => "Pequeños Paquetes Certificado", 'servicio_id' => 15, 'certificado' => true, "activo" => true, "created_at" => now()],
            ["nombre" => "Cecograma", "nombre_referencial" => "Cecograma", 'servicio_id' => 17, 'certificado' => false, "activo" => true, "created_at" => now()],
            ["nombre" => "Certificado", "nombre_referencial" => "Cecograma Certificado", 'servicio_id' => 17, 'certificado' => true, "activo" => true, "created_at" => now()],
            ["nombre" => "Sacas M", "nombre_referencial" => "Sacas M", 'servicio_id' => 18, 'certificado' => false, "activo" => true, "created_at" => now()],
            ["nombre" => "Certificado", "nombre_referencial" => "Sacas M Certificado", 'servicio_id' => 18, 'certificado' => true, "activo" => true, "created_at" => now()],
            ["nombre" => "Express Mail Service (EMS)", "nombre_referencial" => "Express Mail Service (EMS)", 'servicio_id' => 21, 'certificado' => false, "activo" => true, "created_at" => now()],
            ["nombre" => "Certificado", "nombre_referencial" => "Express Mail Service (EMS) Certificado", 'servicio_id' => 21, 'certificado' => true, "activo" => true, "created_at" => now()],
            ["nombre" => "IposPlus", "nombre_referencial" => "Iposplus", 'servicio_id' => 10, 'certificado' => true, "activo" => true, "created_at" => now()],
            ["nombre" => "Bultos Postales", "nombre_referencial" => "Bultos Postales", 'servicio_id' => 23, 'certificado' => false, "activo" => true, "created_at" => now()],
            ["nombre" => "Cliente Corporativo", "nombre_referencial" => "Cliente Corporativo", 'servicio_id' => 24, 'certificado' => true, "activo" => true, "created_at" => now()],
        ];


        foreach ($sacas as $saca) {
            TipoSaca::updateOrCreate([
                'nombre' => $saca['nombre'],
                'nombre_referencial' => $saca['nombre_referencial'],
            ], [
                'servicio_id' => $saca['servicio_id'],
                'certificado' => $saca['certificado'],
                'activo' => $saca['activo'],
            ]);
        }
    }
}
