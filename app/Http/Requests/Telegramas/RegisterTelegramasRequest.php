<?php

namespace App\Http\Requests\Telegramas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterTelegramasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|min:2|max:255',
            'apellido' => 'required|string|min:2|max:255',
            'correo' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('sispven_app.usuarios')
                        ->where('correo', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Este correo ya está registrado.');
                    }
                }
            ],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'password' => 'required|string|min:4|max:255',
            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->format('Y-m-d'), // Mínimo 18 años
                'after_or_equal:' . now()->subYears(90)->format('Y-m-d')   // Máximo 90 años
            ],
            'cedula' => [
                'required',
                'integer',
                'min:100000', // Mínimo 6 dígitos
                'max:999999999999999', // Máximo 15 dígitos
                function ($attribute, $value, $fail) {
                    $exists = DB::table('sispven_app.usuarios')
                        ->where('cedula', $value)
                        ->where('tipo_documento', $this->input('tipo_documento'))
                        ->exists();
                    
                    if ($exists) {
                        $fail('Esta cédula ya está registrada.');
                    }
                }
            ],
            'tipo_documento' => [
                'required',
                'string',
                Rule::in(['V', 'E', 'J', 'C', 'G', 'P'])
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.min' => 'El apellido debe tener al menos 2 caracteres.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Debe proporcionar un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 4 caracteres.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'Debes tener al menos 18 años para registrarte.',
            'fecha_nacimiento.after_or_equal' => 'La fecha de nacimiento no es válida.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.min' => 'La cédula debe tener al menos 6 dígitos.',
            'cedula.max' => 'La cédula no puede tener más de 15 dígitos.',
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in' => 'El tipo de documento debe ser V, E, J, C, G o P.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $message = $errors->first();

        // Mensajes específicos para el contrato de API
        if ($errors->has('correo')) {
            $correoError = $errors->first('correo');
            if (str_contains($correoError, 'ya está registrado')) {
                $message = 'El correo ya está registrado';
            }
        }
        if ($errors->has('cedula')) {
            $cedulaError = $errors->first('cedula');
            if (str_contains($cedulaError, 'ya está registrada')) {
                $message = 'La cédula ya está registrada';
            }
        }

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $message,
        ], 422));
    }
}
