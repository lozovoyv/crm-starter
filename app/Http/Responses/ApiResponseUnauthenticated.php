<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponseUnauthenticated extends ApiResponse
{
    protected int $statusCode = self::CODE_UNAUTHENTICATED;

    /**
     * Get response.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse([
            'message' => $this->message ?? null
        ], $this->statusCode, $this->getHeaders());
    }
}
