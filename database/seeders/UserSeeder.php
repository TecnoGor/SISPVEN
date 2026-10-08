<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario Admin
        User::firstOrCreate(
            ['cedula' => 'V-123456789'],
            [
                'name' => 'SuperAdmin',
                'email' => 'admin1@email.com',
                'oficina_id' => 2,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('SuperAdmin');

        User::firstOrCreate(
            ['cedula' => 'V-123456788'],
            [
                'name' => 'CPI',
                'email' => 'cpi@email.com',
                'oficina_id' => 1,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('SuperAdmin');

        User::firstOrCreate(
            ['cedula' => 'V-30520662'],
            [
                'name' => 'OPT chacao',
                'email' => 'opt1@email.com',
                'oficina_id' => 38,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Jefe de OPT');

        User::firstOrCreate(
            ['cedula' => 'V-4561278'],
            [
                'name' => 'Operador Integral',
                'email' => 'alexis@email.com',
                'oficina_id' => 38,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Promotor Integral');

        User::firstOrCreate(
            ['cedula' => 'V-4561276'],
            [
                'name' => 'Operador Integral',
                'email' => 'alexis2@email.com',
                'oficina_id' => 38,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Promotor Integral');


        User::firstOrCreate(
            ['cedula' => 'V-9654783'],
            [
                'name' => 'reyner',
                'email' => 'reyner@email.com',
                'oficina_id' => 38,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Supervisor Postal');

        User::firstOrCreate(
            ['cedula' => 'V-4574121'],
            [
                'name' => 'Clasificador Postal',
                'email' => 'abisai@email.com',
                'oficina_id' => 38,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Clasificador Postal');

        User::firstOrCreate(
            ['cedula' => 'V-30541874'],
            [
                'name' => 'Cop San Martin',
                'email' => 'cop1@email.com',
                'oficina_id' => 10,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Gerente de Estado');

        User::firstOrCreate(
            ['cedula' => 'V-5632147'],
            [
                'name' => 'jefe1',
                'email' => 'jefe1@email.com',
                'oficina_id' => 10,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Jefe de Division Operativa');

        User::firstOrCreate(
            ['cedula' => 'V-5632647'],
            [
                'name' => 'jefeadministrativocent',
                'email' => 'jefeadmincent@email.com',
                'oficina_id' => 4,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Jefe de Division Administrativa');


        User::firstOrCreate(
            ['cedula' => 'V-5832147'],
            [
                'name' => 'jefeadministrativocop',
                'email' => 'jefeadmincop@email.com',
                'oficina_id' => 10,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Jefe de Division Administrativa');

        User::firstOrCreate(
            ['cedula' => 'V-5632149'],
            [
                'name' => 'coordinador1',
                'email' => 'coordinador1@email.com',
                'oficina_id' => 10,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Coordinador Comercial');

        User::firstOrCreate(
            ['cedula' => 'V-19229048'],
            [
                'name' => 'Cop Barinas',
                'email' => 'cop2@email.com',
                'oficina_id' => 6,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Gerente de Estado');

        User::firstOrCreate(
            ['cedula' => 'V-19654447'],
            [
                'name' => 'OPT Barinas',
                'email' => 'opt2@email.com',
                'oficina_id' => 78,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Jefe de OPT');

        User::firstOrCreate(
            ['cedula' => 'V-34423122'],
            [
                'name' => 'Clasificador Postal',
                'email' => 'abisai2@email.com',
                'oficina_id' => 78,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Clasificador Postal');


        User::firstOrCreate(
            ['cedula' => 'V-11223344'],
            [
                'name' => 'Presidente',
                'email' => 'presidente@email.com',
                'oficina_id' => 1,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Presidente');

        User::firstOrCreate(
            ['cedula' => 'V-12223344'],
            [
                'name' => 'Analista de Direccion Comercial',
                'email' => 'comercial@email.com',
                'oficina_id' => 38,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Analista de Direccion Comercial');

        User::firstOrCreate(
            ['cedula' => 'V-3232311'],
            [
                'name' => 'Aduana',
                'email' => 'aduana@email.com',
                'oficina_id' => 10,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Jefe de Aduana');

        User::firstOrCreate(
            ['cedula' => 'V-19654957'],
            [
                'name' => 'Promotor Cent',
                'email' => 'cent@email.com',
                'oficina_id' => 3,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Promotor Integral');

        User::firstOrCreate(
            ['cedula' => 'V-30666422'],
            [
                'name' => 'Operador Integral',
                'email' => 'luis@email.com',
                'oficina_id' => 35,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Promotor Integral');

        User::firstOrCreate(
            ['cedula' => 'V-30103010'],
            [
                'name' => 'OPT propatria',
                'email' => 'opt3@email.com',
                'oficina_id' => 35,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Jefe de OPT');

        User::firstOrCreate(
            ['cedula' => 'V-13666999'],
            [
                'name' => 'Clasificador Propatria',
                'email' => 'roberto@email.com',
                'oficina_id' => 35,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Clasificador Postal');

        User::firstOrCreate(
            ['cedula' => 'V-30541894'],
            [
                'name' => 'Cop Maracay',
                'email' => 'cop5@email.com',
                'oficina_id' => 18,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Gerente de Estado');

        User::firstOrCreate(
            ['cedula' => 'V-19655547'],
            [
                'name' => 'OPT Barinas',
                'email' => 'opt8@email.com',
                'oficina_id' => 77,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Jefe de OPT');

        User::firstOrCreate(
            ['cedula' => 'V-13925999'],
            [
                'name' => 'Clasificador Propatria',
                'email' => 'optbarinas@email.com',
                'oficina_id' => 77,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Clasificador Postal');

        User::updateOrCreate(
            ['cedula' => 'V-30888888'],
            [
                'name' => 'Cop Delta Amacuro',
                'email' => 'cop6@email.com',
                'oficina_id' => 24,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Gerente de Estado');

        User::updateOrCreate(
            ['cedula' => 'V-11111111'],
            [
                'name' => 'CNC',
                'email' => 'cnc@email.com',
                'oficina_id' => 2,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('CNC');

        User::firstOrCreate(
            ['cedula' => 'V-27423122'],
            [
                'name' => 'Clasificador Postal',
                'email' => 'abisai4@email.com',
                'oficina_id' => 73,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Clasificador Postal');

        User::firstOrCreate(
            ['cedula' => 'V-27433122'],
            [
                'name' => 'Clasificador Bulto Postal',
                'email' => 'clasificadorbulto@email.com',
                'oficina_id' => 38,
                'email_verified_at' => now(),
                'password' => bcrypt('123456789'),
                'activo' => true,
            ]
        )->assignRole('Clasificador Bultos');
    }
}
