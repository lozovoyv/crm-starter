<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponseCommon extends ApiResponse
{
    protected int $statusCode = self::CODE_OK;

    protected mixed $data;

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
            static::combine(['data' => $this->data ?? null], [
                'message' => $this->message,
                'payload' => $this->payload,
            ]), $this->statusCode, $this->getHeaders()
        );
    }

    /**
     * Response data.
     *
     * @param mixed $data
     *
     * @return  $this
     */
    public function data(mixed $data): self
    {
        $this->data = $data;

        return $this;
    }
}
