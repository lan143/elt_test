<?php

namespace App\Http\Requests;

class UpdateMessageRequest extends FormRequest
{
    protected function authorize(): bool
    {
        return true;
    }

    protected function rules(): array
    {
        return [
            'message' => 'required|string',
        ];
    }
}
