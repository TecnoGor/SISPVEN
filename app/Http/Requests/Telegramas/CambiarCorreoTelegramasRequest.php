<?php

namespace App\Http\Requests\Telegramas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CambiarCorreoTelegramasRequest extends FormRequest
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
            'password' => 'required|string',
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
            'password.required' => 'La contraseña es obligatoria.',
            'correo_nuevo.required' => 'El correo nuevo es obligatorio.',
            'correo_nuevo.email' => 'El correo nuevo debe ser válido.',
            'correo_nuevo.different' => 'El correo nuevo debe ser diferente al actual.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $message = $errors->first();

        if ($errors->has('correo_nuevo') && str_contains($errors->first('correo_nuevo'), 'ya está registrado')) {
            $message = 'El correo nuevo ya está en uso';
        }

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $message,
        ], 422));
    }
}
