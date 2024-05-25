<?php

namespace App\Http\Requests\Modulos;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ModulosUpdateRequest extends FormRequest
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
            'nombre'            => 'required|min:3|unique:sedes',
            'image_url'     => 'required',
            'estado'                => 'required'
        ];
    }


    public function messages()
    {
        return [
            'nombre.required'           => 'Ingrese un nombre valido.',
            'nombre.min'                => 'Ingrese un nombre que contenga más de 3 caracteres.',
            'nombre.unique'             => "El nombre ya se encuentra registrado.",
            "image_url.required"        => 'La imagen es requerida',
            'estado.required'     =>       'El estado es requerido.'
        ];
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                [
                    'success' => false,
                    'message' => 'Validar Errores',
                    'errors' => $validator->errors()
                ],
                400
            )
        );
    }
}
