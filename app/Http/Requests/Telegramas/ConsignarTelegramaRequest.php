<?php

namespace App\Http\Requests\Telegramas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConsignarTelegramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'remitente'                      => 'required|array',
            'remitente.tipo_documento'       => 'required|string|in:V,E,J,C,G,P',
            'remitente.numero_documento'     => 'required|string|max:20',
            'remitente.nombre'               => 'required|string|max:100',
            'remitente.apellido'             => 'required|string|max:100',
            'remitente.telefono'             => 'nullable|string|max:20',
            'remitente.correo'               => 'nullable|email|max:255',

            'destinatario'                   => 'required|array',
            'destinatario.tipo_documento'    => 'required|string|in:V,E,J,C,G,P',
            'destinatario.numero_documento'  => 'required|string|max:20',
            'destinatario.nombre'            => 'required|string|max:100',
            'destinatario.apellido'          => 'required|string|max:100',
            'destinatario.telefono'          => 'nullable|string|max:20',
            'destinatario.correo'            => 'nullable|email|max:255',
            'destinatario.estado_nombre'     => 'nullable|string|max:100',
            'destinatario.municipio'         => 'nullable|string|max:100',
            'destinatario.parroquia'         => 'nullable|string|max:100',
            'destinatario.ciudad'            => 'nullable|string|max:100',
            'destinatario.direccion'         => 'required|string|max:500',
            'destinatario.codigo_postal'     => 'nullable|string|max:10',

            'texto_telegrama'            => 'required|string|min:3',
            'palabras_tasables'          => 'required|integer|min:1',
            'palabras_reales'            => 'required|integer|min:1',
            'total_a_pagar'              => 'required|numeric|min:0',
            'costo_sin_iva'              => 'required|numeric|min:0',
            'tipo_telegrama'             => 'nullable|string|max:50',
            'metodo_pago'                => 'nullable|string|max:50',
            'oficina_destino_nombre'     => 'nullable|string|max:255',
            'tipo_remitente_id'          => 'nullable|integer',
            'lugar_emision_id'           => 'nullable|integer',
            'circuito_judicial_id'       => 'nullable|integer',
            'circuito_judicial_dest_id'  => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'remitente.required'                     => 'Los datos del remitente son requeridos.',
            'remitente.tipo_documento.in'            => 'Tipo de documento del remitente inválido.',
            'remitente.numero_documento.required'    => 'El número de documento del remitente es requerido.',
            'remitente.nombre.required'              => 'El nombre del remitente es requerido.',
            'remitente.apellido.required'            => 'El apellido del remitente es requerido.',
            'destinatario.required'                  => 'Los datos del destinatario son requeridos.',
            'destinatario.tipo_documento.in'         => 'Tipo de documento del destinatario inválido.',
            'destinatario.numero_documento.required' => 'El número de documento del destinatario es requerido.',
            'destinatario.nombre.required'           => 'El nombre del destinatario es requerido.',
            'destinatario.apellido.required'         => 'El apellido del destinatario es requerido.',
            'destinatario.direccion.required'        => 'La dirección del destinatario es requerida.',
            'texto_telegrama.required'               => 'El texto del telegrama es requerido.',
            'texto_telegrama.min'                    => 'El texto del telegrama debe tener al menos 3 caracteres.',
            'palabras_tasables.required'             => 'El conteo de palabras tasables es requerido.',
            'total_a_pagar.required'                 => 'El total a pagar es requerido.',
            'costo_sin_iva.required'                 => 'El costo sin IVA es requerido.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 422));
    }
}
