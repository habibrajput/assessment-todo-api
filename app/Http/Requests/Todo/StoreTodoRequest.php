<?php

namespace App\Http\Requests\Todo;

use App\Http\Requests\BaseApiRequest;

class StoreTodoRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Please provide a title for this to-do item.',
            'description.required' => 'Please provide a description for this to-do item.',
        ];
    }
}
