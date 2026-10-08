<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ["pais_id" => 90, "region_id" => 1, "nombre" => "Distrito Capital", "codigo" => "VE-A" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 1, "nombre" => "Miranda", "codigo" => "VE-M" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 2, "nombre" => "Barinas", "codigo" => "VE-E" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 2, "nombre" => "Trujillo", "codigo" => "VE-T" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 2, "nombre" => "Merida", "codigo" => "VE-L" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 2, "nombre" => "Tachira", "codigo" => "VE-S" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 3, "nombre" => "Carabobo", "codigo" => "VE-G" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 3, "nombre" => "Cojedes", "codigo" => "VE-H" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 3, "nombre" => "Lara", "codigo" => "VE-K" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 3, "nombre" => "Portuguesa", "codigo" => "VE-P" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 3, "nombre" => "Yaracuy", "codigo" => "VE-U" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 4, "nombre" => "Amazonas", "codigo" => "VE-Z" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 4, "nombre" => "Apure", "codigo" => "VE-C" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 4, "nombre" => "Aragua", "codigo" => "VE-D" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 4, "nombre" => "Guarico", "codigo" => "VE-J" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 5, "nombre" => "Falcón", "codigo" => "VE-I" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 5, "nombre" => "Zulia", "codigo" => "VE-V" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 6, "nombre" => "Anzoátegui", "codigo" => "VE-B" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 6, "nombre" => "Bolívar", "codigo" => "VE-F" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 6, "nombre" => "Delta Amacuro", "codigo" => "VE-Y" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 6, "nombre" => "Monagas", "codigo" => "VE-N" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 6, "nombre" => "Nueva Esparta", "codigo" => "VE-O" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 6, "nombre" => "Sucre", "codigo" => "VE-R" , "activo" => true, "created_at" => now()],
            ["pais_id" => 90, "region_id" => 1, "nombre" => "La Guaira", "codigo" => "VE-X" , "activo" => true, "created_at" => now()],
        ];

        foreach ($estados as $estado) {
            Estado::firstOrCreate(
                ['pais_id' => $estado['pais_id'], 'region_id' => $estado['region_id'], 'nombre' => $estado['nombre'], 'codigo' => $estado['codigo'], 'activo' => $estado['activo']],
                ['created_at' => now()]
            );
        }

        $codigos = [
            'Distrito Capital' => 'DC',
            'Miranda' => 'MR',
            'Barinas' => 'BA',
            'Trujillo' => 'TR',
            'Merida' => 'ME',
            'Tachira' => 'TA',
            'Carabobo' => 'CB',
            'Cojedes' => 'CO',
            'Lara' => 'LR',
            'Portuguesa' => 'PT',
            'Yaracuy' => 'YA',
            'Amazonas' => 'AM',
            'Apure' => 'AP',
            'Aragua' => 'AR',
            'Guárico' => 'GU',
            'Falcón' => 'FA',
            'Zulia' => 'ZU',
            'Anzoátegui' => 'AN',
            'Bolívar' => 'BO',
            'Delta Amacuro' => 'DA',
            'Monagas' => 'MO',
            'Nueva Esparta' => 'NE',
            'Sucre' => 'SU',
            'La Guaira' => 'LG'
        ];

        foreach($codigos as $est => $cod ){
            Estado::where('nombre', $est)->update(['codigo' => $cod]);
        }  
    }
}
