<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsumoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('insumos')->insert([
            ['descripcion' => 'KIT BOLSA EMS DE 02 A 10 KG. (Bolsa, precinto y porta guía)', 'costo' => 14.50, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'CAJA EEB DE 5 KG', 'costo' => 13.34, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'CAJA EEB DE 3 KG', 'costo' => 8.67, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'GUÍAS DE CONSIGNACIÓN EEB/EMS/LOG', 'costo' => 6.10, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'PORTA GUÍAS', 'costo' => 4.73, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'BOLSAS GRIS', 'costo' => 5.00, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'SOBRES CON VENTANA', 'costo' => 2.01, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'SOBRES CARTA', 'costo' => 3.01, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'SOBRES MEDIA CARTA', 'costo' => 2.55, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'SOBRES PLASTICO 2 (Tamaño Oficio)', 'costo' => 3.00, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'HOJA DE PAPEL TAMAÑO CARTA', 'costo' => 2.54, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'ETIQUETA AUTOADHESIVA (Alto 3x3 cm x Ancho 8 cm) C/U', 'costo' => 3.07, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'PRECINTOS', 'costo' => 2.51, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'BOLSA PLASTICA 2 KL (Cada una)', 'costo' => 2.66, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'BOLSA PLASTICA 5 KL (Cada una)', 'costo' => 5.19, 'activo' => true, "created_at" => now()],
            ['descripcion' => 'BOLSA PLASTICA 10 KL (Cada una)', 'costo' => 7.27, 'activo' => true, "created_at" => now()],
        ]);
    }
}
