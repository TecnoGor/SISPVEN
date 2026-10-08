<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AsignarRolBaseCorrespondencia extends Command
{
    protected $signature = 'correspondencia:asignar-rol-base';

    protected $description = 'Asigna el rol "Usuario Correspondencia" a todos los usuarios que no lo poseen (excepto SuperAdmin)';

    public function handle(): int
    {
        $usuarios = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Usuario Correspondencia');
        })->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'SuperAdmin');
        })->get();

        if ($usuarios->isEmpty()) {
            $this->info('Todos los usuarios ya poseen el rol base de correspondencia.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($usuarios->count());
        $bar->start();

        foreach ($usuarios as $user) {
            $user->assignRole('Usuario Correspondencia');
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Rol asignado a {$usuarios->count()} usuario(s) exitosamente.");

        return self::SUCCESS;
    }
}
