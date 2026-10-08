<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Permiso de la pantalla web de recolectas de oficina. Seeder aparte para
 * no tocar RoleSeeder. Se asigna a SuperAdmin y a los mismos roles que ya
 * tienen "Ver telegramas" (los roles operativos de oficina).
 */
class RecolectaPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permiso = Permission::firstOrCreate(['name' => 'Ver recolectas']);

        $roles = Role::whereHas('permissions', fn ($q) => $q->where('name', 'Ver telegramas'))
            ->orWhere('name', 'SuperAdmin')
            ->get();

        foreach ($roles as $rol) {
            if (!$rol->hasPermissionTo($permiso)) {
                $rol->givePermissionTo($permiso);
            }
        }
    }
}
