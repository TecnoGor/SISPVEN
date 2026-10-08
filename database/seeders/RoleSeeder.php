<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin = Role::updateOrCreate(['id' => 1], ['name' => 'SuperAdmin']);
        $role1 = Role::updateOrCreate(['id' => 2], ['name' => 'Gerente de Estado']);
        $role2 = Role::updateOrCreate(['id' => 3], ['name' => 'Jefe de OPT']);
        $role3 = Role::updateOrCreate(['id' => 4], ['name' => 'Supervisor Postal']);
        $role4 = Role::updateOrCreate(['id' => 5], ['name' => 'Promotor Integral']);
        $role5 = Role::updateOrCreate(['id' => 6], ['name' => 'Clasificador Postal']);
        $role6 = Role::updateOrCreate(['id' => 7], ['name' => 'Repartidor Postal Telegrafico']);
        $role7 = Role::updateOrCreate(['id' => 8], ['name' => 'Auxiliar de Operaciones']);
        $role15 = Role::updateOrCreate(['id' => 9], ['name' => 'Chofer']);

        $role8 = Role::updateOrCreate(['id' => 10], ['name' => 'Jefe de Division Administrativa']);
        $role9 = Role::updateOrCreate(['id' => 11], ['name' => 'Jefe de Division Operativa']);
        $role10 = Role::updateOrCreate(['id' => 12], ['name' => 'Coordinador Comercial']);
        $role11 = Role::updateOrCreate(['id' => 13], ['name' => 'Coordinador Operativo']);
        $role12 = Role::updateOrCreate(['id' => 14], ['name' => 'Operaciones Nacionales']);
        $role13 = Role::updateOrCreate(['id' => 15], ['name' => 'CNC']);
        $role14 = Role::updateOrCreate(['id' => 16], ['name' => 'Analista de Direccion Comercial']);
        $role16 = Role::updateOrCreate(['id' => 17], ['name' => 'Presidente']);
        $role17 = Role::updateOrCreate(['id' => 18], ['name' => 'Gerente de Encaminamiento']);
        $role18 = Role::updateOrCreate(['id' => 19], ['name' => 'Jefe de Aduana']);
        $role19 = Role::updateOrCreate(['id' => 20], ['name' => 'Apertura CPC']);
        $role25 = Role::updateOrCreate(['id' => 21], ['name' => 'Inspector Postal']);
        $role21 = Role::updateOrCreate(['id' => 22], ['name' => 'Clasificador Bultos']);
        $role22 = Role::updateOrCreate(['id' => 23], ['name' => 'Clasificador EMS']);
        $role23 = Role::updateOrCreate(['id' => 24], ['name' => 'Telegrafista']);
        $role24 = Role::updateOrCreate(['id' => 25], ['name' => 'Soporte']);
        $role26 = Role::updateOrCreate(['id' => 26], ['name' => 'Supervisor']);
        $role27 = Role::updateOrCreate(['id' => 28], ['name' => 'Jefe de Oficina Unipersonal']);
        $role29 = Role::updateOrCreate(['id' => 30], ['name' => 'Director Operaciones']);
        $role30 = Role::updateOrCreate(['id' => 31], ['name' => 'Auxiliar Operaciones General']);
        $role31 = Role::updateOrCreate(['id' => 32], ['name' => 'Coodinador Operativo General']);
        $role32 = Role::updateOrCreate(['id' => 33], ['name' => 'Jefe De Division General']);
        $role33 = Role::updateOrCreate(['id' => 34], ['name' => 'Supervisor Operativo']);
        $role34 = Role::updateOrCreate(['id' => 35], ['name' => 'Dir Comercial']);
        $role35 = Role::updateOrCreate(['id' => 36], ['name' => 'Clasificador Postal General']);
        $role36 = Role::updateOrCreate(['id' => 37], ['name' => 'Reparto']);

        $role_pc = Role::updateOrCreate(['id' => 38], ['name' => 'Presidente Correspondencia']);
        $role_dc = Role::updateOrCreate(['id' => 39], ['name' => 'Director Correspondencia']);
        $role_gc = Role::updateOrCreate(['id' => 40], ['name' => 'Gerente Correspondencia']);
        $role_ac = Role::updateOrCreate(['id' => 41], ['name' => 'Analista Correspondencia']);
        $role_uc = Role::updateOrCreate(['id' => 42], ['name' => 'Usuario Correspondencia']);

        $role_37 = Role::updateOrCreate(['id' => 43], ['name' => 'Director(a) Comercial']);
        $role_38 = Role::updateOrCreate(['id' => 44], ['name' => 'Gerente De Operaciones']);

        $secuencia = DB::select("SELECT pg_get_serial_sequence('roles', 'id') as seq")[0]->seq;
        DB::statement("SELECT setval('$secuencia', (SELECT MAX(id) FROM roles))");


        //Modulos del Sidebar
        Permission::firstOrCreate(['name' => 'Ver Guias de Despacho'])->assignRole([$role2, $role3, $role5, $role1, $role10, $role9, $role7]);
        Permission::firstOrCreate(['name' => 'Ver Listado de Ventas'])->assignRole([$role4, $admin, $role2]);
        Permission::firstOrCreate(['name' => 'Ver estadisticas'])->assignRole([$admin, $role16]);
        Permission::firstOrCreate(['name' => 'Rutas Nacionales'])->assignRole([$admin, $role1, $role17]);
        Permission::firstOrCreate(['name' => 'Rutas'])->assignRole([$role1, $admin, $role17]);
        Permission::firstOrCreate(['name' => 'Ver Vehiculos'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Control flota'])->assignRole([$admin, $role1, $role2]);
        Permission::firstOrCreate(['name' => 'Ver servicios'])->assignRole([$role1, $role2, $admin]);
        Permission::firstOrCreate(['name' => 'Ver usuarios'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Ver Roles'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Ver Oficinas'])->assignRole([$role1, $role2, $role4, $role8, $role9, $admin, $role5,  $role7]);
        Permission::firstOrCreate(['name' => 'Ver Oficinas-admin'])->assignRole([$role1, $role9, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Mi oficina'])->assignRole([$role4, $role2, $role3]);
        Permission::firstOrCreate(['name' => 'Ver envios'])->assignRole([$role2,  $role4, $admin, $role1, $role3]);
        Permission::firstOrCreate(['name' => 'Ver tasas'])->assignRole([$role1, $role2,  $role4, $admin]);
        Permission::firstOrCreate(['name' => 'Ver valijas'])->assignRole([$admin, $role1, $role3,  $role5,   $role7]);
        Permission::firstOrCreate(['name' => 'Ver parametros'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Gestionar Integrantes'])->assignRole([$role13, $admin]);
        Permission::firstOrCreate(['name' => 'Servicios especiales'])->assignRole([$role2, $role14, $admin]);
        Permission::firstOrCreate(['name' => 'Acceso Modulo Correspondencia'])->assignRole([$role_pc, $role_dc, $role_gc, $role_ac, $role_uc, $admin]);

        //Acciones\
        Permission::firstOrCreate(['name' => 'Realizar Salidas de Despacho'])->assignRole([$role5, $role1, $role7]);
        Permission::firstOrCreate(['name' => 'Realizar Entradas de Despacho'])->assignRole([$role5, $role1,  $role7]);
        Permission::firstOrCreate(['name' => 'Ver Despachos Disponibles'])->assignRole([$admin, $role2, $role5, $role4, $role1, $role3,  $role7]);
        Permission::firstOrCreate(['name' => 'Consultar envios'])->assignRole([$role1, $role2,  $role4, $role5, $admin, $role4, $role3,   $role7]);
        Permission::firstOrCreate(['name' => 'Crear vehiculos'])->assignRole([$admin, $role2, $role1]);
        Permission::firstOrCreate(['name' => 'Crear Integrantes de Oficina'])->assignRole([$admin, $role2, $role1]);
        Permission::firstOrCreate(['name' => 'Crear envios'])->assignRole([$role4, $admin]);
        Permission::firstOrCreate(['name' => 'Crear usuarios'])->assignRole([$role1, $admin]);
        Permission::firstOrCreate(['name' => 'Editar Usuarios'])->assignRole([$role1, $admin]);
        Permission::firstOrCreate(['name' => 'Cambiar roles'])->assignRole([$role1, $admin]);
        Permission::firstOrCreate(['name' => 'Crear roles'])->assignRole([$role1, $admin]);
        Permission::firstOrCreate(['name' => 'Eliminar Roles'])->assignRole([$role1, $admin]);
        Permission::firstOrCreate(['name' => 'Editar Roles'])->assignRole([$role1, $admin]);
        Permission::firstOrCreate(['name' => 'Editar Permisos'])->assignRole([$role1, $admin]);
        Permission::firstOrCreate(['name' => 'Crear oficinas'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Eliminar oficinas'])->assignRole([$role1, $admin]);
        Permission::firstOrCreate(['name' => 'Generar Apartados'])->assignRole([$role2, $admin]);
        Permission::firstOrCreate(['name' => 'Visualizar Apartados'])->assignRole([$role2, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Integrantes de Oficina'])->assignRole([$role2, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Vehiculos de Oficina'])->assignRole([$role2, $admin]);
        Permission::firstOrCreate(['name' => 'Servicios extras'])->assignRole([$role2, $admin, $role4]);
        Permission::firstOrCreate(['name' => 'Ver Exporta facil'])->assignRole([$admin, $role4]);
        Permission::firstOrCreate(['name' => 'Clientes corporativos'])->assignRole([$role14, $admin]);
        Permission::firstOrCreate(['name' => 'Crear rutas locales'])->assignRole([$role1]);
        Permission::firstOrCreate(['name' => 'Crear rutas Nacionales'])->assignRole([$admin, $role1, $role17]);
        Permission::firstOrCreate(['name' => 'Crear Viajes locales'])->assignRole([$role1]);
        Permission::firstOrCreate(['name' => 'Crear Viajes Nacionales'])->assignRole([$admin, $role1, $role17]);
        Permission::firstOrCreate(['name' => 'Ver telegramas'])->assignRole([$admin, $role4]);
        Permission::firstOrCreate(['name' => 'Operaciones'])->assignRole([$role2, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Incidencias'])->assignRole([$role4, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Insumos'])->assignRole([$role8, $role2, $role4, $admin]);
        Permission::firstOrCreate(['name' => 'Enviar Insumos'])->assignRole([$role8, $admin]);
        Permission::firstOrCreate(['name' => 'Ingresar Insumos'])->assignRole([$role8, $role2, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Inventario General de Insumos'])->assignRole([$role10, $admin]);
        Permission::firstOrCreate(['name' => 'Aperturar Envios'])->assignRole([$role19, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Gastos Operativos'])->assignRole([$role2, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Recibos Consignacion'])->assignRole([$role4, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Reportes Presidencia'])->assignRole([$role1, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Apostilla'])->assignRole([$role4, $admin]);
        Permission::firstOrCreate(['name' => 'Ver gestion clientes'])->assignRole([$role14, $admin]);
        Permission::firstOrCreate(['name' => 'Ver personal autorizado'])->assignRole([$role14, $admin]);
        Permission::firstOrCreate(['name' => 'Ver detalle contratos'])->assignRole([$role14, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Gastos Servicios'])->assignRole([$role2, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Insumos Usuarios'])->assignRole([$role2, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Gastos Arrendamiento'])->assignRole([$role2, $admin]);
        Permission::firstOrCreate(['name' => 'Entrega en Agencia'])->assignRole([$role4]);
        Permission::firstOrCreate(['name' => 'Crear Oficinas Aliadas'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Ver Semáforo Postal'])->assignRole([$admin, $role16]);
        Permission::firstOrCreate(['name' => 'Seguimiento de Instrucciones Asignadas'])->assignRole([$role_pc, $admin]);

        // Permisos individuales por vista de correspondencia
        Permission::firstOrCreate(['name' => 'Ver Correspondencia-Presidente'])->assignRole([$role_pc]);
        Permission::firstOrCreate(['name' => 'Ver Correspondencia-Director'])->assignRole([$role_dc]);
        Permission::firstOrCreate(['name' => 'Ver Correspondencia-Gerente'])->assignRole([$role_gc]);
        Permission::firstOrCreate(['name' => 'Ver Correspondencia-Analista'])->assignRole([$role_ac]);
        Permission::firstOrCreate(['name' => 'Ver Correspondencia-Usuario'])->assignRole([$role_uc]);
        Permission::firstOrCreate(['name' => 'Ver Correspondencia-Admin'])->assignRole([$admin]);

        Permission::firstOrCreate(['name' => 'Asignar Comunicados'])->assignRole([$role_pc, $role_dc, $role_gc]);
        Permission::firstOrCreate(['name' => 'Atender Asignaciones'])->assignRole([$role_ac]);
        Permission::firstOrCreate(['name' => 'Ver Reportes Correspondencia'])->assignRole([$role_pc, $role_dc, $admin]);
        Permission::firstOrCreate(['name' => 'Ver Auditoria Correspondencia'])->assignRole([$admin]);

        Permission::firstOrCreate(['name' => 'Ver Talento Humano'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Ver Registro de Empleados'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Ver Asignacion de Jefe en Opts'])->assignRole([$admin, $role1]);
        Permission::firstOrCreate(['name' => 'Vehiculos Totales Control Flota']);

        Permission::firstOrCreate(['name' => 'Ver Informacion de Envíos'])->assignRole([$admin, $role13]);
        Permission::firstOrCreate(['name' => 'Ver Asignacion de Gerente Estado'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Ver Asignacion de Presidente'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Ver Unidad de Analisis de Devolucion'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Ver Rezagos'])->assignRole([$admin]);
        Permission::firstOrCreate(['name' => 'Enviar a Sala'])->assignRole([$admin, $role1]);


        // $permiso = Permission::where('name', 'Operaciones')->first();

        // if($permiso){
        //     $permiso->name = 'Apertura/Cierre de Oficina';
        //     $permiso->save();
        // }


    }
}
