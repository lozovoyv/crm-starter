<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponseSuccess extends ApiResponse
{
    protected int $statusCode = self::CODE_OK;

    /**
     * Get response.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            static::combine(
                ['message' => $this->message ?? null],
                ['payload' => $this->payload ?? null]
            ), $this->statusCode, $this->getHeaders()
        );
    }
}
