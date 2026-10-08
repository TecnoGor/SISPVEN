<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambiarContraseñaRequest extends FormRequest
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
            'correo' => 'required|email',
            'contraseña_actual' => 'required|string',
            'contraseña_nueva' => 'required|string|min:4|max:255|different:contraseña_actual',
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
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Debe proporcionar un correo electrónico válido.',
            'contraseña_actual.required' => 'La contraseña actual es obligatoria.',
            'contraseña_nueva.required' => 'La contraseña nueva es obligatoria.',
            'contraseña_nueva.min' => 'La contraseña nueva debe tener al menos 4 caracteres.',
            'contraseña_nueva.different' => 'La contraseña nueva debe ser diferente a la actual.',
        ];
    }
}
