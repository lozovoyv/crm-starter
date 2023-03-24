<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponseTokenMismatch extends ApiResponse
{
    protected int $statusCode = self::CODE_TOKEN_MISMATCH;

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
