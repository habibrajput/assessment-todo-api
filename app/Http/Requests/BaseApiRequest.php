<?php

namespace App\Http\Requests;

use App\libs\Response\GlobalApiResponse;
use App\libs\Response\GlobalApiResponseCodeBook;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        $errors = collect($validator->errors()->toArray())
            ->map(fn($msgs) => $msgs[0])
            ->toArray();

        $response = (new GlobalApiResponse())->error(
            GlobalApiResponseCodeBook::INVALID_FORM_INPUTS,
            'The provided data is invalid. Please fix the errors and try again.',
            $errors
        );

        throw new HttpResponseException(
            response()->json($response, 422)
        );
    }
}
