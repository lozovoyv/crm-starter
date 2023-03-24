<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Throwable;

class ApiResponseServerError extends ApiResponse
{
    protected int $statusCode = self::CODE_SERVER_ERROR;

    protected Throwable $exception;

    /**
     * Response payload.
     *
     * @param Throwable $exception
     *
     * @return  $this
     */
    public function exception(Throwable $exception): self
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * Get response.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse($this->convertExceptionToArray($this->exception), $this->statusCode, $this->getHeaders());
    }

    /**
     * Convert the given exception to an array.
     *
     * @param Throwable $exception
     *
     * @return array
     */
    protected function convertExceptionToArray(Throwable $exception): array
    {
        return config('app.debug') ? [
            'message' => $exception->getMessage(),
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => collect($exception->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all(),
        ] : [
            'message' => 'Server error',
        ];
    }
}
