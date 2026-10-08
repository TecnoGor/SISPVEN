<?php

namespace App\Http\Requests\Clientes;

use App\Rules\CodigosTelefono;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Validación de la solicitud de recolecta. Paridad con las reglas del
 * módulo Iposplus web (App\Livewire\Iposplus\Iposplus::rules() y la regla
 * de suma de dimensiones de agregarAlLote()), más la ubicación GPS del
 * punto de recolecta (payload en inglés: latitude/longitude/gps_accuracy).
 */
class CrearRecolectaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ownership por token (middleware cliente)
    }

    public function rules(): array
    {
        return [
            // Paquete
            'modo_peso' => 'required|in:manual,volumetrico',
            'peso' => 'nullable|required_if:modo_peso,manual|numeric|min:0.001',
            'alto' => 'nullable|required_if:modo_peso,volumetrico|numeric|min:0.001|max:300',
            'ancho' => 'nullable|required_if:modo_peso,volumetrico|numeric|min:0.001|max:300',
            'largo' => 'nullable|required_if:modo_peso,volumetrico|numeric|min:0.001|max:300',
            'contenido' => 'required|max:300',

            // Remitente
            'nombre_rem' => 'required|max:20',
            'apellido_rem' => 'required|max:20',
            'tipo_documento_rem' => 'required',
            'documento_rem' => 'required|digits_between:6,12',
            'telefono_rem' => ['required', new CodigosTelefono],
            'correo_rem' => 'required|email',

            // Punto de recolecta (ubicación del cliente)
            'estado_id' => 'required|integer|exists:estados,estado_id',
            'municipio_id' => 'required|integer|exists:municipios,municipio_id',
            'parroquia_id' => 'required|integer|exists:parroquias,parroquia_id',
            'ciudad_id' => 'required|integer|exists:ciudades,ciudad_id',
            'codigo_postal' => 'required',
            'direccion' => 'required|max:200',
            'referencia' => 'nullable|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'gps_accuracy' => 'nullable|numeric|min:0',
            'gps_manual' => 'nullable|boolean',

            // Destinatario
            'nombre_dest' => 'required|max:20',
            'apellido_dest' => 'required|max:20',
            'tipo_documento_dest' => 'required',
            'documento_dest' => 'required|digits_between:6,12',
            'telefono_dest' => ['required', new CodigosTelefono],
            'correo_dest' => 'required|email|max:50',
            'estado_dest_id' => 'required|integer|exists:estados,estado_id',
            'municipio_dest_id' => 'required|integer|exists:municipios,municipio_id',
            'parroquia_dest_id' => 'required|integer|exists:parroquias,parroquia_id',
            'ciudad_dest_id' => 'required|integer|exists:ciudades,ciudad_id',
            'codigo_postal_dest' => 'required',
            'direccion_dest' => 'required|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo es obligatorio',
            'required_if' => 'El campo es obligatorio',
            'direccion.max' => 'Máximo de 200 caracteres',
            'direccion_dest.max' => 'Máximo de 200 caracteres',
            'correo_rem.email' => 'El formato del correo debe ser válido',
            'correo_dest.email' => 'El formato del correo debe ser válido',
            'latitude.required' => 'Debe indicar la ubicación GPS del punto de recolecta',
            'longitude.required' => 'Debe indicar la ubicación GPS del punto de recolecta',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // Réplica de la validación de agregarAlLote(): la suma de
            // dimensiones no debe superar 300 cm.
            if ($this->input('modo_peso') === 'volumetrico') {
                $suma = (float) $this->input('alto') + (float) $this->input('ancho') + (float) $this->input('largo');
                if ($suma > 300) {
                    $v->errors()->add('alto', 'La suma de alto, ancho y largo no debe superar 300 cm.');
                }
            }

            // Coherencia de la cascada de ubicación (la web la garantiza por
            // UI; en la API se valida explícitamente).
            $this->validarCascada($v, 'municipio_id', 'estado_id', \App\Models\Municipio::class, 'estado_id');
            $this->validarCascada($v, 'parroquia_id', 'municipio_id', \App\Models\Parroquia::class, 'municipio_id');
            $this->validarCascada($v, 'ciudad_id', 'municipio_id', \App\Models\Ciudad::class, 'municipio_id');
            $this->validarCascada($v, 'municipio_dest_id', 'estado_dest_id', \App\Models\Municipio::class, 'estado_id');
            $this->validarCascada($v, 'parroquia_dest_id', 'municipio_dest_id', \App\Models\Parroquia::class, 'municipio_id');
            $this->validarCascada($v, 'ciudad_dest_id', 'municipio_dest_id', \App\Models\Ciudad::class, 'municipio_id');
        });
    }

    private function validarCascada(Validator $v, string $campoHijo, string $campoPadre, string $modelo, string $columnaPadre): void
    {
        $hijoId = $this->input($campoHijo);
        $padreId = $this->input($campoPadre);

        if (!$hijoId || !$padreId || $v->errors()->has($campoHijo)) {
            return;
        }

        $hijo = $modelo::find($hijoId);
        if ($hijo && (int) $hijo->{$columnaPadre} !== (int) $padreId) {
            $v->errors()->add($campoHijo, 'La ubicación seleccionada no corresponde a la jerarquía indicada.');
        }
    }
}
