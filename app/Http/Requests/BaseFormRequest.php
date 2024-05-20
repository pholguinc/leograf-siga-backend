<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class BaseFormRequest extends FormRequest
{
    use ResponseTrait;

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->responseErrorJson('errors', $validator->errors(), 422));
    }
}
