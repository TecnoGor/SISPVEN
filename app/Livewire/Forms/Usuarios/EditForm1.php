<?php

namespace App\Livewire\Forms\Usuarios;

use Livewire\Form;
use App\Models\User;
use App\Rules\CodigosTelefono;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use App\Models\UsuarioSeguimiento;

class EditForm1 extends Form
{
    public $usuario_id = '';
    public $open = false;

    #[Validate]
    public $name;

    #[Validate]
    public $email;

    #[Validate]
    public $password;

    public $cedula;

    public $tipo_documento = 'V';
    public $numero_documento;

    public $telefono;

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->usuario_id)],
            'password' => ['nullable', 'string', 'min:4', 'max:32'], // Hacer que el password sea opcional
            'tipo_documento' => ['required', 'in:V,E,J,G,P'],
            'numero_documento' => ['required', 'string', 'max:10'],
            'cedula' => [
                'required',
                'string',
                'max:12',
                Rule::unique('users', 'cedula')->ignore($this->usuario_id),
            ],
            'telefono' => ['nullable', new CodigosTelefono],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.lowercase' => 'El correo electrónico debe estar en minúsculas.',
            'email.email' => 'Por favor, ingresa un correo electrónico válido.',
            'email.unique' => 'El correo electrónico ya está registrado. Por favor, verifica los datos.',
            'cedula.unique' => 'La cédula ya está registrada. Por favor, verifica los datos.',
            'cedula.required' => 'La cédula es obligatoria.',
            'tipo_documento.required' => 'Debe seleccionar el tipo de documento.',
            'tipo_documento.in' => 'Tipo de documento no válido.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'numero_documento.max' => 'El número no puede exceder 10 caracteres.',
            'password.min' => 'La contraseña debe tener al menos 4 caracteres.',
            'password.max' => 'La contraseña no puede exceder los 32 caracteres.',
            'telefono.required' => 'El teléfono es obligatorio.',
        ];
    }
    

    public function edit(User $usuario)
    {
        $this->open = true;
        $this->usuario_id = $usuario->id;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        $this->cedula = $usuario->cedula;

        // Separar tipo y número desde la cédula almacenada (formato "V-12345678")
        if ($usuario->cedula && strpos($usuario->cedula, '-') !== false) {
            [$tipo, $numero] = explode('-', $usuario->cedula, 2);
            $this->tipo_documento = $tipo;
            $this->numero_documento = $numero;
        } else {
            $this->tipo_documento = 'V';
            $this->numero_documento = $usuario->cedula;
        }

        $this->telefono = $usuario->telefono;
        $this->password = ''; // Resetear el campo de password
    }

    public function update()
    {
        // Reconstruir el campo cedula antes de validar para mantener compatibilidad
        $this->cedula = $this->tipo_documento . '-' . $this->numero_documento;

        $this->validate();

        $usuario = User::find($this->usuario_id);

        // Actualizar solo los campos permitidos
        $usuario->name = $this->name;
        $usuario->email = $this->email;
        $usuario->cedula = $this->cedula;
        $usuario->telefono = $this->telefono;

        if (!empty($this->password)) {
            $usuario->password = bcrypt($this->password); // Usar bcrypt para encriptar el password
        }

        $usuario->save();

        $user = auth()->user();
        UsuarioSeguimiento::create([
            'usuario_id' => $usuario->id,
            'accion' => 'update',
            'descripcion' => "Usuario $user->id hizo un update del usuario $usuario->id"
        ]);

        $this->open = false;
    }
}
