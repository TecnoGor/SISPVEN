<?php

namespace App\Livewire\AsignacionRoles;

use App\Models\User;
use App\Models\Empleado;
use Livewire\Component;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class AsignacionPresidente extends Component
{
    // Búsqueda
    public $documento_busqueda = '';
    public $usuario_encontrado = null;   // User si ya tiene cuenta
    public $empleado_encontrado = null;   // Empleado si no tiene cuenta aún
    public $busqueda_realizada = false;

    // Modo crear cuenta desde empleado
    public $clave;

    // Cambiar contraseña del presidente actual
    public $modal_cambiar_clave = false;
    public $nueva_clave;

    public function buscarPorDocumento()
    {
        $this->validate([
            'documento_busqueda' => 'required|string|min:6',
        ], [
            'documento_busqueda.required' => 'Debe ingresar un documento.',
            'documento_busqueda.min'      => 'El documento debe tener al menos 6 caracteres.',
        ]);

        $this->reset(['usuario_encontrado', 'empleado_encontrado', 'clave']);
        $this->busqueda_realizada = true;

        $documento = trim($this->documento_busqueda);

        // 1. Buscar primero en users (cedula completa o sin prefijo)
        $usuario = User::where('cedula', $documento)
            ->orWhere('cedula', 'V-' . $documento)
            ->orWhere('cedula', 'E-' . $documento)
            ->first();

        if ($usuario) {
            $this->usuario_encontrado = $usuario;
            return;
        }

        // 2. Si no hay usuario, buscar en empleados
        $empleado = Empleado::where('documento', $documento)
            ->whereDoesntHave('user')
            ->first();

        if ($empleado) {
            $this->empleado_encontrado = $empleado;
            return;
        }

        $this->dispatch('alertError', message: 'No se encontró ningún usuario o empleado con ese documento.');
    }

    public function asignar()
    {
        // Validar según el flujo
        if ($this->usuario_encontrado) {
            // Usuario existente: no requiere clave
        } elseif ($this->empleado_encontrado) {
            $this->validate([
                'clave' => 'required|string|min:8',
            ], [
                'clave.required' => 'La contraseña es obligatoria para crear la cuenta.',
                'clave.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            ]);
        } else {
            $this->dispatch('alertError', message: 'Primero debe buscar y encontrar un usuario o empleado.');
            return;
        }

        DB::beginTransaction();

        try {
            $auditor = auth()->user();

            // 1. Si ya hay un Presidente, quitarle el rol
            $presidenteAnterior = User::role('Presidente')->first();
            if ($presidenteAnterior) {
                $presidenteAnterior->removeRole('Presidente');

                UsuarioSeguimiento::create([
                    'usuario_id'  => $auditor->id,
                    'accion'      => 'update',
                    'descripcion' => "Se removió el rol Presidente al usuario ({$presidenteAnterior->id})",
                ]);
            }

            // 2. Resolver el nuevo presidente
            if ($this->usuario_encontrado) {
                $nuevoPresidente = User::find($this->usuario_encontrado->id);
            } else {
                $empleado = Empleado::find($this->empleado_encontrado->empleado_id);
                $cedulaCompleta = $empleado->tipo_documento . $empleado->documento;

                $nuevoPresidente = User::create([
                    'empleado_id' => $empleado->empleado_id,
                    'name'        => $empleado->nombre . ' ' . $empleado->apellido,
                    'email'       => $empleado->correo,
                    'cedula'      => $cedulaCompleta,
                    'telefono'    => $empleado->telefono,
                    'password'    => Hash::make($this->clave),
                    'oficina_id'  => $empleado->oficina_id,
                ]);
            }

            // 3. Reemplazar todos los roles por solo Presidente
            $nuevoPresidente->syncRoles(['Presidente']);

            UsuarioSeguimiento::create([
                'usuario_id'  => $auditor->id,
                'accion'      => 'update',
                'descripcion' => "Se asignó el rol Presidente al usuario ({$nuevoPresidente->id})",
            ]);

            DB::commit();

            $this->dispatch('alertSuccess', message: 'Presidente asignado correctamente.');
            $this->resetBusqueda();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error asignando Presidente: ' . $e->getMessage());
            $this->dispatch('alertError', message: 'Ocurrió un error al asignar el presidente. Intente de nuevo.');
        }
    }

    public function removerPresidente()
    {
        DB::beginTransaction();

        try {
            $presidente = User::role('Presidente')->first();

            if (!$presidente) {
                $this->dispatch('alertError', message: 'No hay Presidente asignado actualmente.');
                DB::rollback();
                return;
            }

            $presidente->removeRole('Presidente');

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se removió el rol Presidente al usuario ({$presidente->id})",
            ]);

            DB::commit();

            $this->dispatch('alertSuccess', message: 'Presidente removido correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error removiendo Presidente: ' . $e->getMessage());
            $this->dispatch('alertError', message: 'Ocurrió un error al remover el presidente. Intente de nuevo.');
        }
    }

    public function abrirCambiarClave()
    {
        $this->nueva_clave = '';
        $this->modal_cambiar_clave = true;
    }

    public function cerrarCambiarClave()
    {
        $this->modal_cambiar_clave = false;
        $this->nueva_clave = '';
    }

    public function cambiarClave()
    {
        $this->validate([
            'nueva_clave' => 'required|string|min:8',
        ], [
            'nueva_clave.required' => 'La contraseña es obligatoria.',
            'nueva_clave.min'      => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $presidente = User::role('Presidente')->first();

        if (!$presidente) {
            $this->dispatch('alertError', message: 'No hay Presidente asignado.');
            return;
        }

        $presidente->password = Hash::make($this->nueva_clave);
        $presidente->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se cambió la contraseña del Presidente ({$presidente->id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Contraseña actualizada correctamente.');
        $this->cerrarCambiarClave();
    }

    private function resetBusqueda()
    {
        $this->reset([
            'documento_busqueda',
            'usuario_encontrado',
            'empleado_encontrado',
            'clave',
            'busqueda_realizada',
        ]);
    }

    public function render()
    {
        $presidenteActual = User::role('Presidente')->with('oficina')->first();

        return view('livewire.asignacion-roles.asignacion-presidente', [
            'presidenteActual' => $presidenteActual,
        ]);
    }
}