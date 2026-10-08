<?php

namespace App\Livewire\Forms\OficinaDetalles;

use Livewire\Form;
use App\Models\User;
use App\Models\Empleado;
use App\Models\UsuarioSeguimiento;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateForm extends Form
{
    public $id_oficina;
    public $open = false;

    // Modo: 'empleado' o 'manual'
    public $modo = 'empleado';

    // Modo empleado
    public $empleado_id;

    // Modo manual
    public $nombre;
    public $email;
    public $tipo_documento = 'V';
    public $numero_documento;
    public $telefono;

    // Común
    public $clave;
    public $role_id;

    protected $messages = [
        'empleado_id.required' => 'Debe seleccionar un empleado.',
        'empleado_id.exists'   => 'El empleado seleccionado no es válido.',
        'nombre.required'          => 'El nombre es obligatorio.',
        'email.required'           => 'El correo es obligatorio.',
        'email.email'              => 'El correo debe ser un correo válido.',
        'numero_documento.required' => 'El documento es obligatorio.',
        'numero_documento.min'      => 'El documento debe tener al menos 6 caracteres.',
        'telefono.required'        => 'El teléfono es obligatorio.',
        'clave.required'       => 'La contraseña es obligatoria.',
        'clave.min'            => 'La contraseña debe tener al menos 8 caracteres.',
        'role_id.required'     => 'Debe seleccionar un rol.',
        'role_id.exists'       => 'El rol seleccionado no es válido.',
    ];

    public function create($oficina_id)
    {
        $this->open = true;
        $this->id_oficina = $oficina_id;
        $this->modo = 'empleado';
    }

    public function store()
    {
        if ($this->modo === 'empleado') {
            return $this->storeDesdeEmpleado();
        }

        return $this->storeManual();
    }

    /**
     * Crear usuario a partir de un empleado existente (flujo original).
     */
    private function storeDesdeEmpleado()
    {
        $this->validate([
            'empleado_id' => 'required|exists:empleados,empleado_id',
            'clave'       => 'required|string|min:8',
            'role_id'     => 'required|exists:roles,id',
        ]);

        $empleado = Empleado::find($this->empleado_id);

        if (!$empleado || $empleado->oficina_id != $this->id_oficina) {
            session()->flash('error', 'El empleado seleccionado no pertenece a esta oficina.');
            return;
        }

        if (User::where('empleado_id', $empleado->empleado_id)->exists()) {
            session()->flash('error', 'Este empleado ya tiene una cuenta de usuario asociada.');
            return;
        }

        $cedulaCompleta = $empleado->tipo_documento . '-' . $empleado->documento;

        $documento = $empleado->documento;
        if (User::where('cedula', 'like', '%-' . $documento)
            ->orWhere('cedula', 'V' . $documento)
            ->orWhere('cedula', 'E' . $documento)
            ->exists()) {
            $this->addError('empleado_id', 'El número de documento ' . $documento . ' ya está registrado en otro usuario.');
            return;
        }

        $rolSeleccionado = Role::find($this->role_id);
        if ($rolSeleccionado) {
            $configuracionRol = \App\Models\OficinaPersonal::where('oficina_id', $this->id_oficina)
                ->where('rol_id', $rolSeleccionado->id)
                ->first();

            if ($configuracionRol) {
                $usuariosConRol = User::where('oficina_id', $this->id_oficina)
                    ->whereHas('roles', function ($query) use ($rolSeleccionado) {
                        $query->where('id', $rolSeleccionado->id);
                    })
                    ->count();

                if ($usuariosConRol >= $configuracionRol->cantidad_max) {
                    session()->flash('error', "No se puede registrar un nuevo usuario. Se ha alcanzado el límite máximo ({$configuracionRol->cantidad_max}) para el rol '{$rolSeleccionado->name}'.");
                    return;
                }
            }
        }

        $user = User::create([
            'empleado_id' => $empleado->empleado_id,
            'name'        => $empleado->nombre . ' ' . $empleado->apellido,
            'email'       => $empleado->correo,
            'cedula'      => $cedulaCompleta,
            'telefono'    => $empleado->telefono,
            'password'    => Hash::make($this->clave),
            'oficina_id'  => $this->id_oficina,
        ]);

        if ($rolSeleccionado) {
            $user->assignRole($rolSeleccionado);
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => $user->id,
            'accion'      => 'create',
            'descripcion' => "Se ha creado un Usuario a la oficina (desde empleado)",
        ]);

        $this->reset(['empleado_id', 'clave', 'role_id', 'modo']);
        $this->modo = 'empleado';
        $this->open = false;
        return 'ok';
    }

    /**
     * Crear usuario manualmente (sin empleado registrado).
     */
    private function storeManual()
    {
        $this->validate([
            'nombre'           => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'tipo_documento'   => 'required|in:V,E',
            'numero_documento' => 'required|string|min:6|max:15',
            'telefono'         => 'required|string|max:20',
            'clave'            => 'required|string|min:8',
            'role_id'          => 'required|exists:roles,id',
        ]);

        $cedulaCompleta = $this->tipo_documento . '-' . $this->numero_documento;

        $documento = $this->numero_documento;
        if (User::where('cedula', 'like', '%-' . $documento)
            ->orWhere('cedula', 'V' . $documento)
            ->orWhere('cedula', 'E' . $documento)
            ->exists()) {
            $this->addError('numero_documento', 'El número de documento ' . $documento . ' ya está registrado en otro usuario.');
            return;
        }

        $rolSeleccionado = Role::find($this->role_id);
        if ($rolSeleccionado) {
            $configuracionRol = \App\Models\OficinaPersonal::where('oficina_id', $this->id_oficina)
                ->where('rol_id', $rolSeleccionado->id)
                ->first();

            if ($configuracionRol) {
                $usuariosConRol = User::where('oficina_id', $this->id_oficina)
                    ->whereHas('roles', function ($query) use ($rolSeleccionado) {
                        $query->where('id', $rolSeleccionado->id);
                    })
                    ->count();

                if ($usuariosConRol >= $configuracionRol->cantidad_max) {
                    session()->flash('error', "No se puede registrar un nuevo usuario. Se ha alcanzado el límite máximo ({$configuracionRol->cantidad_max}) para el rol '{$rolSeleccionado->name}'.");
                    return;
                }
            }
        }

        $user = User::create([
            'name'       => $this->nombre,
            'email'      => $this->email,
            'cedula'     => $cedulaCompleta,
            'telefono'   => $this->telefono,
            'password'   => Hash::make($this->clave),
            'oficina_id' => $this->id_oficina,
        ]);

        if ($rolSeleccionado) {
            $user->assignRole($rolSeleccionado);
        }

        UsuarioSeguimiento::create([
            'usuario_id'  => $user->id,
            'accion'      => 'create',
            'descripcion' => "Se ha creado un Usuario a la oficina (manual)",
        ]);

        $this->reset(['nombre', 'email', 'tipo_documento', 'numero_documento', 'telefono', 'clave', 'role_id', 'modo']);
        $this->modo = 'empleado';
        $this->tipo_documento = 'V';
        $this->open = false;
        return 'ok';
    }
}
