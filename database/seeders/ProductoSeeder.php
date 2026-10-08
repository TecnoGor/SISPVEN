<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
            'nombre' => 'Tarjeta Postal',
            'activo' => true,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::firstOrCreate(
            ['nombre' => $producto['nombre']],
            ['activo' => $producto['activo']]
            );
        }

        
    }
}
