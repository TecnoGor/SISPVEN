<?php

namespace App\Http\Requests\Telegramas;

use Illuminate\Foundation\Http\FormRequest;

class ResetearContrasenaTelegramasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'correo' => 'required|email',
            'otp' => 'required|string|size:6',
            'password_nueva' => 'required|string|min:4|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Debe proporcionar un correo electrónico válido.',
            'otp.required' => 'El código de verificación es obligatorio.',
            'otp.size' => 'El código debe tener 6 dígitos.',
            'password_nueva.required' => 'La contraseña nueva es obligatoria.',
            'password_nueva.min' => 'La contraseña nueva debe tener al menos 4 caracteres.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 422));
    }
}
