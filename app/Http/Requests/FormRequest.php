<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;

abstract class FormRequest extends Request
{
    /**
     * @throws ValidationException
     */
    public function validate(): void
    {
        if (false === $this->authorize()) {
            throw new UnauthorizedException();
        }

        $validator = validator($this->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            throw new ValidationException(
                $validator,
                new Response(
                    $validator->errors(),
                    422
                )
            );
        }
    }

    protected abstract function authorize(): bool;

    protected abstract function rules(): array;

    protected function messages(): array
    {
        return [];
    }
}
