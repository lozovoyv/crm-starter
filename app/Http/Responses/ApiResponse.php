<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Http\Responses;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Throwable;

abstract class ApiResponse implements Responsable
{
    public const CODE_OK = 200;
    public const CODE_REDIRECT = 301;
    public const CODE_NOT_MODIFIED = 304;
    public const CODE_ERROR = 400;
    public const CODE_UNAUTHENTICATED = 401;
    public const CODE_FORBIDDEN = 403;
    public const CODE_NOT_FOUND = 404;
    public const CODE_TOKEN_MISMATCH = 419;
    public const CODE_VALIDATION_ERROR = 422;

    public const CODE_SERVER_ERROR = 500;

    protected int $statusCode;
    protected array $headers;
    protected ?Carbon $lastModified;

    protected ?string $message = null;
    protected ?array $payload = [];

    public function __construct(int $statusCode = self::CODE_OK, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Combine
     *
     * @param array $common
     * @param array|null $optional
     *
     * @return array
     */
    protected static function combine(array $common, ?array $optional): array
    {
        if (!empty($optional)) {
            foreach ($optional as $key => $value) {
                if (!empty($value)) {
                    $common[$key] = $value;
                }
            }
        }

        return $common;
    }

    /**
     * Message attached to response.
     *
     * @param string|null $message
     *
     * @return  $this
     */
    public function message(?string $message): self
    {
        $this->message = !empty($message) ? trans($message) : null;

        return $this;
    }

    /**
     * Response payload.
     *
     * @param array|null $payload
     *
     * @return  $this
     */
    public function payload(?array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Add last modifier header to response.
     * Modified timestamp must be GMT timezone.
     *
     * @param Carbon|null $timestamp
     *
     * @return ApiResponse
     */
    public function lastModified(?Carbon $timestamp): self
    {
        $this->lastModified = $timestamp;

        return $this;
    }

    /**
     * Compose headers for response.
     *
     * @return  array
     */
    protected function getHeaders(): array
    {
        $headers = $this->headers;

        if (isset($this->lastModified)) {
            $headers['Last-Modified'] = $this->lastModified->clone()->setTimezone('GMT')->format('D, d M Y H:i:s') . ' GMT';
        }

        return $headers;
    }

    /**
     * Common API response factory.
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseCommon
     */
    public static function common(mixed $data, int $statusCode = self::CODE_OK, array $headers = []): ApiResponseCommon
    {
        return (new ApiResponseCommon($statusCode, $headers))->data($data);
    }

    /**
     * Not found API response factory.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseNotFound
     */
    public static function notFound(string $message = 'Не найдено', int $statusCode = self::CODE_NOT_FOUND, array $headers = []): ApiResponseNotFound
    {
        return (new ApiResponseNotFound($statusCode, $headers))->message($message);
    }

    /**
     * Not modified API response factory.
     *
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseNotModified
     */
    public static function notModified(int $statusCode = self::CODE_NOT_MODIFIED, array $headers = []): ApiResponseNotModified
    {
        return (new ApiResponseNotModified($statusCode, $headers));
    }

    /**
     * Token mismatch API response factory.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseTokenMismatch
     */
    public static function tokenMismatch(string $message = 'Неверный токен', int $statusCode = self::CODE_TOKEN_MISMATCH, array $headers = []): ApiResponseTokenMismatch
    {
        return (new ApiResponseTokenMismatch($statusCode, $headers))->message($message);
    }

    /**
     * Token mismatch API response factory.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseForbidden
     */
    public static function forbidden(string $message = 'Доступ запрещён', int $statusCode = self::CODE_FORBIDDEN, array $headers = []): ApiResponseForbidden
    {
        return (new ApiResponseForbidden($statusCode, $headers))->message($message);
    }

    /**
     * Unauthenticated API response factory.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseUnauthenticated
     */
    public static function unauthenticated(string $message = 'Доступ запрещён', int $statusCode = self::CODE_UNAUTHENTICATED, array $headers = []): ApiResponseUnauthenticated
    {
        return (new ApiResponseUnauthenticated($statusCode, $headers))->message($message);
    }

    /**
     * Success API response factory.
     *
     * @param string|null $message
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseSuccess
     */
    public static function success(?string $message = null, int $statusCode = self::CODE_OK, array $headers = []): ApiResponseSuccess
    {
        return (new ApiResponseSuccess($statusCode, $headers))->message($message);
    }

    /**
     * Success API response factory.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseError
     */
    public static function error(string $message, int $statusCode = self::CODE_ERROR, array $headers = []): ApiResponseError
    {
        return (new ApiResponseError($statusCode, $headers))->message($message);
    }

    /**
     * Success API response factory.
     *
     * @param Throwable $exception
     * @param int $statusCode
     * @param array $headers
     *
     * @return ApiResponseServerError
     */
    public static function serverError(Throwable $exception, int $statusCode = self::CODE_SERVER_ERROR, array $headers = []): ApiResponseServerError
    {
        return (new ApiResponseServerError($statusCode, $headers))->exception($exception);
    }

    /**
     * Form API response factory.
     *
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseForm
     */
    public static function form(int $statusCode = self::CODE_OK, array $headers = []): ApiResponseForm
    {
        return new ApiResponseForm($statusCode, $headers);
    }

    /**
     * Form validation error API response factory.
     *
     * @param array $errors
     * @param string|null $message
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseValidationError
     */
    public static function validationError(array $errors, ?string $message = 'Не все поля корректно заполнены', int $statusCode = self::CODE_VALIDATION_ERROR, array $headers = []
    ): ApiResponseValidationError
    {
        return (new ApiResponseValidationError($statusCode, $headers))->errors($errors)->message($message);
    }

    /**
     * List API response factory.
     *
     * @param array|Arrayable|Collection|LengthAwarePaginator $list
     * @param int $statusCode
     * @param array $headers
     *
     * @return  ApiResponseList
     */
    public static function list(array|Arrayable|Collection|LengthAwarePaginator $list = [], int $statusCode = self::CODE_OK, array $headers = []): ApiResponseList
    {
        return (new ApiResponseList($statusCode, $headers))->items($list);
    }

}
