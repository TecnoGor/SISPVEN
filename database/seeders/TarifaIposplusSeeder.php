<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TarifaIposplusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tarifas_iposplus')->insert([

            ['kilo_min' => 0.1,    'kilo_max' => 1,   'precio' => 2.51, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 1.01,   'kilo_max' => 2,  'precio' => 4.37, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 2.01,   'kilo_max' => 3,   'precio' => 6.24, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 3.01,   'kilo_max' => 4,   'precio' => 8.10, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 4.01,   'kilo_max' => 5,   'precio' => 9.97, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 5.01,   'kilo_max' => 6,   'precio' => 11.84, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 6.01,   'kilo_max' => 7,   'precio' => 13.70, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 7.01,   'kilo_max' => 8,   'precio' => 13.91, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 8.01,   'kilo_max' => 9,   'precio' => 15.78, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 9.01,  'kilo_max' => 10,   'precio' => 15.99, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 10.01,  'kilo_max' => 11,   'precio' => 17.85, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 11.01,  'kilo_max' => 12,   'precio' => 18.06, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 12.01,  'kilo_max' => 13,   'precio' => 19.93, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 13.01,  'kilo_max' => 14,   'precio' => 20.14, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 14.01,  'kilo_max' => 15,   'precio' => 22.01, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 15.01,  'kilo_max' => 16,   'precio' => 22.22, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 16.01,  'kilo_max' => 17,   'precio' => 24.08, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 17.01,  'kilo_max' => 18,   'precio' => 24.28, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 18.01,  'kilo_max' => 19,   'precio' => 26.16, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 19.01,  'kilo_max' => 20,   'precio' => 26.36, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 20.01,  'kilo_max' => 21,   'precio' => 26.57, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 21.01,  'kilo_max' => 22,   'precio' => 28.43, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 22.01,  'kilo_max' => 23,   'precio' => 28.64, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 23.01,  'kilo_max' => 24,   'precio' => 28.85, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 24.01,  'kilo_max' => 25,   'precio' => 30.56, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 25.01,  'kilo_max' => 26,   'precio' => 30.77, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 26.01,  'kilo_max' => 27,   'precio' => 30.98, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 27.01,  'kilo_max' => 28,   'precio' => 33.01, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 28.01,  'kilo_max' => 29,   'precio' => 33.22, 'activo' => true, 'created_at' => now()],

            ['kilo_min' => 29.01,  'kilo_max' => 30,   'precio' => 33.42, 'activo' => true, 'created_at' => now()],

        ]);
    }
}
