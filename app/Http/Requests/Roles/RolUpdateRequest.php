<?php

namespace App\Http\Requests\Roles;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RolUpdateRequest extends FormRequest
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
            'rol'            => 'required|min:3',
            'estado'                => 'required'
        ];
    }


    public function messages()
    {
        return [
            'rol.required'           => 'Ingrese un nombre valido.',
            'rol.min'                => 'Ingrese un nombre que contenga más de 3 caracteres.',
            'rol.unique'             => "El nombre ya se encuentra registrado.",
            'estado.required'     =>    'El estado es requerido.'
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
