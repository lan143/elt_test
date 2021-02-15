<?php

namespace App\Http\Requests;

class CreateMessageRequest extends FormRequest
{
    protected function authorize(): bool
    {
        return true;
    }

    protected function rules(): array
    {
        return [
            'message' => 'required|string',
            'parent' => 'string'
        ];
    }
}
