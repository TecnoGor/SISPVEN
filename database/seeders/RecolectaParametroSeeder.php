<?php

namespace Database\Seeders;

use App\Models\Parametro as ParametroModel;
use Illuminate\Database\Seeder;

/**
 * Crea el parámetro de tarifa "Recoleccion IposPlus" (tarifa fija en Bs
 * del servicio de recolección a domicilio de la app de clientes).
 *
 * Idempotente por nombre: NO toca el seeder base Parametro (insert plano
 * posicional). En producción ejecutar aislado:
 *   php artisan db:seed --class=RecolectaParametroSeeder
 *
 * El valor es editable después desde la pantalla /ver-tasas.
 */
class RecolectaParametroSeeder extends Seeder
{
    public function run(): void
    {
        ParametroModel::firstOrCreate(
            ['nombre' => config('recolectas.parametro_recoleccion')],
            ['valor' => 0, 'activo' => true]
        );
    }
}
