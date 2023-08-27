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
        if (config('app.debug')) {
            $vendorPath = base_path('vendor');

            $message = str_replace('`', '\'', $exception->getMessage());
            return [
                'message' => $message,
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => collect($exception->getTrace())->map(function ($trace) use ($vendorPath) {
                    $trace['is_vendor'] = isset($trace['file']) && str_starts_with($trace['file'], $vendorPath);
                    return Arr::except($trace, ['args']);
                })->all(),
            ];
        }

        return [
            'message' => 'Server error',
        ];
    }
}
