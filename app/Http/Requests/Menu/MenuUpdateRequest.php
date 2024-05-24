<?php

namespace App\Http\Requests\Menu;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MenuUpdateRequest extends FormRequest
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
            'nombre'            => 'required|min:3',
            'id_modulo'        => 'required',
        ];
    }


    public function messages()
    {
        return [
            'nombre.required'           => 'Ingrese un nombre valido.',
            'id_modulo.required'    => 'El id del módulo es requerido',
            'nombre.min'                => 'Ingrese un nombre que contenga más de 3 caracteres.',
            'nombre.unique'             => "El nombre ya se encuentra registrado.",
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
