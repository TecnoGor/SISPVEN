<?php

namespace App\Livewire\GestionCorrespondencia;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CorrespondenciaMain extends Component
{
    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$user->hasPermissionTo('Acceso Modulo Correspondencia')) {
            abort(403, 'No tiene acceso al módulo de correspondencia.');
        }

        // Detectar permiso y redirigir
        if ($user->hasPermissionTo('Ver Correspondencia-Presidente')) {
            return redirect()->route('correspondencia.presidente');
        } elseif ($user->hasPermissionTo('Ver Correspondencia-Director')) {
            return redirect()->route('correspondencia.director');
        } elseif ($user->hasPermissionTo('Ver Correspondencia-Gerente')) {
            return redirect()->route('correspondencia.gerente');
        } elseif ($user->hasPermissionTo('Ver Correspondencia-Analista')) {
            return redirect()->route('correspondencia.analista');
        } elseif ($user->hasPermissionTo('Ver Correspondencia-Usuario')) {
            return redirect()->route('correspondencia.usuario');
        }

        // Si tiene permiso de admin de correspondencia
        if ($user->hasPermissionTo('Ver Correspondencia-Admin')) {
            return redirect()->route('correspondencia.admin');
        }

        abort(403, 'Rol de correspondencia no reconocido.');
    }

    public function render()
    {
        return <<<'HTML'
            <div>
                {{-- Cargando... --}}
            </div>
        HTML;
    }
}
