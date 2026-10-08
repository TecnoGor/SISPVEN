<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class CambiarCorreoRequest extends FormRequest
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
            'correo_actual' => 'required|email',
            'contraseña' => 'required|string',
            'correo_nuevo' => [
                'required',
                'email',
                'max:255',
                'different:correo_actual',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('sispven_app.usuarios')
                        ->where('correo', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Este correo ya está registrado.');
                    }
                }
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
            'correo_actual.required' => 'El correo actual es obligatorio.',
            'correo_actual.email' => 'Debe proporcionar un correo electrónico válido.',
            'contraseña.required' => 'La contraseña es obligatoria.',
            'correo_nuevo.required' => 'El correo nuevo es obligatorio.',
            'correo_nuevo.email' => 'El correo nuevo debe ser válido.',
            'correo_nuevo.different' => 'El correo nuevo debe ser diferente al actual.',
        ];
    }
}
