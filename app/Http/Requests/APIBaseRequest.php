<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\ApiUnauthorizedException;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

abstract class APIBaseRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     *
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator, ApiResponse::validationError($validator->errors()->toArray())->toResponse($this));
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws ApiUnauthorizedException
     */
    protected function failedAuthorization(): void
    {
        throw new ApiUnauthorizedException();
    }
}
