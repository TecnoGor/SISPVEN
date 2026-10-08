<?php

namespace App\Livewire\AsignacionRoles;

use App\Models\User;
use App\Models\Estado;
use App\Models\Empleado;
use Livewire\Component;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class AsignacionGerenteEstado extends Component
{
    // Estados secundarios manejados por un mismo gerente
    // estado_id_principal => [ids secundarios]
    private const ESTADOS_SECUNDARIOS = [
        1  => [2, 24],   // Distrito Capital → La Guaira, Miranda
        20 => [21],      // Monagas → Delta Amacuro
    ];

    // IDs que NO aparecen en el select (son cubiertos por otro estado principal)
    private const ESTADOS_EXCLUIDOS = [2, 24, 21];

    // Selección de estado
    public $estado_seleccionado;

    // Modo de asignación: 'existente' o 'nuevo'
    public $modo = 'existente';

    // Modo usuario existente
    public $usuario_seleccionado;

    // Modo crear desde empleado
    public $empleado_seleccionado;
    public $clave;

    // Cambiar contraseña
    public $modal_cambiar_clave = false;
    public $usuario_clave_id;
    public $nueva_clave;

    public function updatedEstadoSeleccionado()
    {
        $this->reset(['usuario_seleccionado', 'empleado_seleccionado', 'clave']);
        $this->modo = 'existente';
    }

    /**
     * Devuelve los IDs de estado a considerar (principal + secundarios si aplica).
     */
    private function estadosCubiertos(): array
    {
        if (!$this->estado_seleccionado) {
            return [];
        }
        $principal = (int) $this->estado_seleccionado;
        $secundarios = self::ESTADOS_SECUNDARIOS[$principal] ?? [];
        return array_merge([$principal], $secundarios);
    }

    public function abrirCambiarClave($userId)
    {
        $this->usuario_clave_id = $userId;
        $this->nueva_clave = '';
        $this->modal_cambiar_clave = true;
    }

    public function cerrarCambiarClave()
    {
        $this->modal_cambiar_clave = false;
        $this->usuario_clave_id = null;
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

        $usuario = User::find($this->usuario_clave_id);

        if (!$usuario) {
            $this->dispatch('alertError', message: 'Usuario no encontrado.');
            return;
        }

        $usuario->password = Hash::make($this->nueva_clave);
        $usuario->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se cambió la contraseña del Gerente de Estado ({$usuario->id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Contraseña actualizada correctamente.');
        $this->cerrarCambiarClave();
    }

    public function asignar()
    {
        if ($this->modo === 'existente') {
            $this->validate([
                'estado_seleccionado'  => 'required',
                'usuario_seleccionado' => 'required|exists:users,id',
            ], [
                'estado_seleccionado.required'  => 'Debe seleccionar un estado.',
                'usuario_seleccionado.required' => 'Debe seleccionar un usuario.',
                'usuario_seleccionado.exists'   => 'El usuario seleccionado no es válido.',
            ]);
        } else {
            $this->validate([
                'estado_seleccionado'    => 'required',
                'empleado_seleccionado'  => 'required|exists:empleados,empleado_id',
                'clave'                  => 'required|string|min:8',
            ], [
                'estado_seleccionado.required'    => 'Debe seleccionar un estado.',
                'empleado_seleccionado.required'  => 'Debe seleccionar un empleado.',
                'empleado_seleccionado.exists'    => 'El empleado seleccionado no es válido.',
                'clave.required'                  => 'La contraseña es obligatoria.',
                'clave.min'                       => 'La contraseña debe tener al menos 8 caracteres.',
            ]);
        }

        DB::beginTransaction();

        try {
            $auditor = auth()->user();

            if ($this->modo === 'existente') {
                $nuevoGerente = User::find($this->usuario_seleccionado);
            } else {
                $empleado = Empleado::find($this->empleado_seleccionado);
                $cedulaCompleta = $empleado->tipo_documento . $empleado->documento;

                $nuevoGerente = User::create([
                    'empleado_id' => $empleado->empleado_id,
                    'name'        => $empleado->nombre . ' ' . $empleado->apellido,
                    'email'       => $empleado->correo,
                    'cedula'      => $cedulaCompleta,
                    'telefono'    => $empleado->telefono,
                    'password'    => Hash::make($this->clave),
                    'oficina_id'  => $empleado->oficina_id,
                ]);
            }

            // Si tenía otro rol, se le quita y se le asigna solo Gerente de Estado
            $nuevoGerente->syncRoles(['Gerente de Estado']);

            UsuarioSeguimiento::create([
                'usuario_id'  => $auditor->id,
                'accion'      => 'update',
                'descripcion' => "Se asignó el rol Gerente de Estado al usuario ({$nuevoGerente->id}) para el estado ({$this->estado_seleccionado})",
            ]);

            DB::commit();

            $this->dispatch('alertSuccess', message: 'Gerente de Estado asignado correctamente.');
            $this->reset(['usuario_seleccionado', 'empleado_seleccionado', 'clave']);
            $this->modo = 'existente';
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error asignando Gerente de Estado: ' . $e->getMessage());
            $this->dispatch('alertError', message: 'Ocurrió un error al asignar el gerente. Intente de nuevo.');
        }
    }

    public function removerRol($userId)
    {
        DB::beginTransaction();

        try {
            $usuario = User::find($userId);

            if (!$usuario) {
                $this->dispatch('alertError', message: 'Usuario no encontrado.');
                DB::rollback();
                return;
            }

            $usuario->removeRole('Gerente de Estado');

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se removió el rol Gerente de Estado al usuario ({$usuario->id})",
            ]);

            DB::commit();

            $this->dispatch('alertSuccess', message: 'Rol removido correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error removiendo rol Gerente de Estado: ' . $e->getMessage());
            $this->dispatch('alertError', message: 'Ocurrió un error al remover el rol. Intente de nuevo.');
        }
    }

    public function render()
    {
        // Estados disponibles para asignar (excluyendo los secundarios)
        $estados = Estado::where('pais_id', 90)
            ->whereNotIn('estado_id', self::ESTADOS_EXCLUIDOS)
            ->orderBy('nombre')
            ->get();

        $estadosCubiertos = $this->estadosCubiertos();

        // Gerentes actuales del estado seleccionado (con sus oficinas)
        $gerentes = collect();
        if ($estadosCubiertos) {
            $gerentes = User::with('oficina.estado')
                ->role('Gerente de Estado')
                ->whereHas('oficina', fn($q) => $q->whereIn('estado_id', $estadosCubiertos)
                    ->whereIn('tipo_oficina_id', [1, 2, 3, 4])
                    ->where('externa', false))
                ->orderBy('name')
                ->get();
        }

        // Usuarios disponibles para asignar (modo "existente")
        $usuariosDisponibles = collect();
        if ($estadosCubiertos) {
            $usuariosDisponibles = User::whereHas('oficina', fn($q) => $q->whereIn('estado_id', $estadosCubiertos)
                    ->whereIn('tipo_oficina_id', [1, 2, 3, 4])
                    ->where('externa', false))
                ->whereDoesntHave('roles', fn($q) => $q->where('name', 'Gerente de Estado'))
                ->orderBy('name')
                ->get();
        }

        // Empleados sin cuenta (modo "nuevo")
        $empleadosDisponibles = collect();
        if ($estadosCubiertos) {
            $empleadosDisponibles = Empleado::whereHas('oficina', fn($q) => $q->whereIn('estado_id', $estadosCubiertos)
                    ->whereIn('tipo_oficina_id', [1, 2, 3, 4])
                    ->where('externa', false))
                ->whereDoesntHave('user')
                ->orderBy('nombre')
                ->get();
        }

        return view('livewire.asignacion-roles.asignacion-gerente-estado', [
            'estados'             => $estados,
            'gerentes'            => $gerentes,
            'usuariosDisponibles' => $usuariosDisponibles,
            'empleadosDisponibles' => $empleadosDisponibles,
        ]);
    }
}
