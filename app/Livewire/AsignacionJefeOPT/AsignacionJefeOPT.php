<?php

namespace App\Livewire\AsignacionJefeOPT;

use App\Models\User;
use App\Models\Oficina;
use App\Models\Empleado;
use Livewire\Component;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class AsignacionJefeOPT extends Component
{
    // Selección de OPT
    public $opt_seleccionada;
    public $jefe_actual = null;

    // Modo de asignación: 'existente' o 'nuevo'
    public $modo = 'existente';

    // Modo usuario existente
    public $usuario_seleccionado;

    // Modo crear desde empleado
    public $empleado_seleccionado;
    public $clave;

    // Cambiar contraseña
    public $modal_cambiar_clave = false;
    public $nueva_clave;

    public function updatedOptSeleccionada($value)
    {
        $this->jefe_actual = null;
        $this->reset(['usuario_seleccionado', 'empleado_seleccionado', 'clave', 'modo']);
        $this->modo = 'existente';

        if ($value) {
            $this->jefe_actual = User::where('oficina_id', $value)
                ->role('Jefe de OPT')
                ->first();
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
        if ($this->nueva_clave) {
            $this->validate([
                'nueva_clave' => 'string|min:8',
            ], [
                'nueva_clave.min' => 'La contraseña debe tener al menos 8 caracteres.',
            ]);
        }

        if (!$this->jefe_actual) {
            $this->dispatch('alertError', message: 'No hay jefe asignado a esta OPT.');
            return;
        }

        // Si no escribió nada, mantener la actual
        if (empty($this->nueva_clave)) {
            $this->dispatch('alertSuccess', message: 'No se realizaron cambios en la contraseña.');
            $this->cerrarCambiarClave();
            return;
        }

        $this->jefe_actual->password = Hash::make($this->nueva_clave);
        $this->jefe_actual->save();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se cambió la contraseña del Jefe de OPT ({$this->jefe_actual->id}) en la oficina ({$this->jefe_actual->oficina->nombre})",
        ]);

        $this->dispatch('alertSuccess', message: 'Contraseña actualizada correctamente.');
        $this->cerrarCambiarClave();
    }

    public function asignar()
    {
        if ($this->modo === 'existente') {
            $this->validate([
                'opt_seleccionada'     => 'required',
                'usuario_seleccionado' => 'required',
            ], [
                'opt_seleccionada.required'     => 'Debe seleccionar una OPT.',
                'usuario_seleccionado.required' => 'Debe seleccionar un usuario.',
            ]);
        } else {
            $this->validate([
                'opt_seleccionada'     => 'required',
                'empleado_seleccionado' => 'required|exists:empleados,empleado_id',
                'clave'                 => 'required|string|min:8',
            ], [
                'opt_seleccionada.required'      => 'Debe seleccionar una OPT.',
                'empleado_seleccionado.required'  => 'Debe seleccionar un empleado.',
                'empleado_seleccionado.exists'    => 'El empleado seleccionado no es válido.',
                'clave.required'                  => 'La contraseña es obligatoria.',
                'clave.min'                       => 'La contraseña debe tener al menos 8 caracteres.',
            ]);
        }

        DB::beginTransaction();

        try {
            $usuario = auth()->user();
            $oficina = Oficina::find($this->opt_seleccionada);

            // 1. Remover jefe actual si existe
            if ($this->jefe_actual) {
                $this->jefe_actual->removeRole('Jefe de OPT');

                UsuarioSeguimiento::create([
                    'usuario_id'  => $usuario->id,
                    'accion'      => 'update',
                    'descripcion' => "Se removió el rol Jefe de OPT al usuario ({$this->jefe_actual->id}) de la oficina ({$oficina->nombre})",
                ]);
            }

            // 2. Determinar el nuevo jefe
            if ($this->modo === 'existente') {
                $nuevoJefe = User::find($this->usuario_seleccionado);
            } else {
                // Crear usuario desde empleado
                $empleado = Empleado::find($this->empleado_seleccionado);
                $cedulaCompleta = $empleado->tipo_documento . $empleado->documento;

                $nuevoJefe = User::create([
                    'empleado_id' => $empleado->empleado_id,
                    'name'        => $empleado->nombre . ' ' . $empleado->apellido,
                    'email'       => $empleado->correo,
                    'cedula'      => $cedulaCompleta,
                    'telefono'    => $empleado->telefono,
                    'password'    => Hash::make($this->clave),
                    'oficina_id'  => $this->opt_seleccionada,
                ]);
            }

            // 3. Si el nuevo jefe tenía otro rol, quitárselo (1 usuario = 1 rol)
            if ($nuevoJefe->roles->isNotEmpty()) {
                $nuevoJefe->syncRoles([]);
            }

            // 4. Asignar rol de Jefe de OPT
            $nuevoJefe->assignRole('Jefe de OPT');

            // 5. Asegurar que el usuario esté asignado a la OPT
            if ($nuevoJefe->oficina_id != $this->opt_seleccionada) {
                $nuevoJefe->oficina_id = $this->opt_seleccionada;
                $nuevoJefe->save();
            }

            // 6. Actualizar campo jefe_oficina en la oficina
            $oficina->jefe_oficina = $nuevoJefe->name;
            $oficina->save();

            // 7. Auditoría
            UsuarioSeguimiento::create([
                'usuario_id'  => $usuario->id,
                'accion'      => 'update',
                'descripcion' => "Se asignó el rol Jefe de OPT al usuario ({$nuevoJefe->id}) en la oficina ({$oficina->nombre})",
            ]);

            DB::commit();

            $this->dispatch('alertSuccess', message: 'Jefe de OPT asignado correctamente.');
            $this->reset(['opt_seleccionada', 'usuario_seleccionado', 'empleado_seleccionado', 'clave', 'jefe_actual']);
            $this->modo = 'existente';
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error asignando Jefe de OPT: ' . $e->getMessage());
            $this->dispatch('alertError', message: 'Ocurrió un error al asignar el jefe. Intente de nuevo.');
        }
    }

    public function render()
    {
        $usuario = auth()->user();
        $miOficina = Oficina::find($usuario->oficina_id);

        // OPTs que gestiona este Gerente (oficina_relacionada_id apunta a su COP)
        $opts = Oficina::where('oficina_relacionada_id', $miOficina->oficina_id)
            ->whereIn('tipo_oficina_id', [1, 2, 3])
            ->orderBy('nombre')
            ->get()
            ->map(function ($opt) {
                $jefe = User::where('oficina_id', $opt->oficina_id)
                    ->role('Jefe de OPT')
                    ->first();
                $opt->jefe_nombre = $jefe ? $jefe->name : null;
                return $opt;
            });

        // Usuarios de la OPT seleccionada (sin rol de Jefe de OPT) para modo "existente"
        $usuariosDisponibles = collect();
        if ($this->opt_seleccionada) {
            $usuariosDisponibles = User::where('oficina_id', $this->opt_seleccionada)
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'Jefe de OPT');
                })
                ->orderBy('name')
                ->get();
        }

        // Empleados sin cuenta de usuario de la OPT seleccionada para modo "nuevo"
        $empleadosDisponibles = collect();
        if ($this->opt_seleccionada) {
            $empleadosDisponibles = Empleado::where('oficina_id', $this->opt_seleccionada)
                ->whereDoesntHave('user')
                ->orderBy('nombre')
                ->get();
        }

        return view('livewire.asignacion-jefe-o-p-t.asignacion-jefe-o-p-t', [
            'opts' => $opts,
            'usuariosDisponibles' => $usuariosDisponibles,
            'empleadosDisponibles' => $empleadosDisponibles,
        ]);
    }
}
