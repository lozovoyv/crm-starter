<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     *
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof TokenMismatchException) {
            return ApiResponse::tokenMismatch()->toResponse($request);
        }

        if ($e instanceof ApiUnauthorizedException) {
            return ApiResponse::unauthenticated()->toResponse($request);
        }

        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        }

        if ($e instanceof Responsable) {
            return $e->toResponse($request);
        }

        $e = $this->prepareException($this->mapException($e));

        if ($response = $this->renderViaCallbacks($request, $e)) {
            return $response;
        }

        return match (true) {
            $e instanceof HttpResponseException => $e->getResponse(),
            $e instanceof AuthenticationException => $this->unauthenticated($request, $e),
            $e instanceof ValidationException => $this->convertValidationExceptionToResponse($e, $request),
            default => $this->renderExceptionResponse($request, $e),
        };
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        return ApiResponse::unauthenticated($exception->getMessage())->toResponse($request);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param ValidationException $e
     * @param Request $request
     *
     * @return Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request): Response
    {
        return ApiResponse::validationError($e->errors(), $e->getMessage())->toResponse($request);
    }

    /**
     * Render a default exception response if any.
     *
     * @param Request $request
     * @param Throwable $e
     *
     * @return Response
     */
    protected function renderExceptionResponse($request, Throwable $e): Response
    {
        return ApiResponse::serverError($e)->toResponse($request);
    }
}
