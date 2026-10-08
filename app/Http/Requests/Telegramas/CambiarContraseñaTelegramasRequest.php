<?php

namespace App\Http\Requests\Telegramas;

use Illuminate\Foundation\Http\FormRequest;

class CambiarContraseñaTelegramasRequest extends FormRequest
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
            'password_actual' => 'required|string',
            'password_nueva' => 'required|string|min:4|max:255|different:password_actual',
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
            'password_actual.required' => 'La contraseña actual es obligatoria.',
            'password_nueva.required' => 'La contraseña nueva es obligatoria.',
            'password_nueva.min' => 'La contraseña nueva debe tener al menos 4 caracteres.',
            'password_nueva.different' => 'La contraseña nueva debe ser diferente a la actual.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 422));
    }
}
