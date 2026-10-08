<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstatusComunicacionSeeder extends Seeder
{
    public function run(): void
    {
        $estatus = [
            [
                'id' => 1,
                'nombre' => 'Pendiente',
                'color_badge' => 'bg-red-100 text-red-800 border-red-200'
            ],
            [
                'id' => 2,
                'nombre' => 'Leído',
                'color_badge' => 'bg-blue-100 text-blue-800 border-blue-200'
            ],
            [
                'id' => 3,
                'nombre' => 'Confirmación de Recibido',
                'color_badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200'
            ],
            [
                'id' => 4,
                'nombre' => 'Respondido',
                'color_badge' => 'bg-orange-100 text-orange-800 border-orange-200'
            ],
            [
                'id' => 5,
                'nombre' => 'Remitido',
                'color_badge' => 'bg-purple-100 text-purple-800 border-purple-200'
            ],
            [
                'id' => 6,
                'nombre' => 'Aprobado / Firmado',
                'color_badge' => 'bg-teal-100 text-teal-800 border-teal-200'
            ],
            [
                'id' => 7,
                'nombre' => 'Devuelto para corregir',
                'color_badge' => 'bg-yellow-100 text-yellow-800 border-yellow-200'
            ],
            [
                'id' => 8,
                'nombre' => 'Rechazado / Archivado',
                'color_badge' => 'bg-gray-100 text-gray-800 border-gray-200'
            ],
            [
                'id' => 9,
                'nombre' => 'Completado',
                'color_badge' => 'bg-green-100 text-green-800 border-green-200'
            ],
        ];

        DB::table('estatus_comunicaciones')->upsert($estatus, ['id'], ['nombre', 'color_badge']);
    }
}
