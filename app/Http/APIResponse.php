<?php

namespace App\Http;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class APIResponse
{
    protected const CODE_REDIRECT = 301;
    protected const CODE_NOT_MODIFIED = 304;
    protected const CODE_FORBIDDEN = 403;
    protected const CODE_NOT_FOUND = 404;
    protected const CODE_ERROR = 406;
    protected const CODE_TOKEN_MISMATCH = 419;
    protected const CODE_OK = 200;
    protected const CODE_VALIDATION_ERROR = 422;

    /**
     * Make 301 redirect response.
     *
     * @param string $to
     * @param string|null $message
     *
     * @return  JsonResponse
     */
    public static function redirect(string $to, ?string $message = 'Перенаправление'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'to' => $to,
        ], self::CODE_REDIRECT);
    }

    /**
     * Make 304 not modified response.
     *
     * @return  JsonResponse
     */
    public static function notModified(): JsonResponse
    {
        return response()->json(null, self::CODE_NOT_MODIFIED);
    }

    /**
     * Make 403 response.
     *
     * @param string $message
     *
     * @return  JsonResponse
     */
    public static function forbidden(string $message = 'Доступ запрещён'): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], self::CODE_FORBIDDEN);
    }

    /**
     * Make 404 response.
     *
     * @param string $message
     *
     * @return  JsonResponse
     */
    public static function notFound(string $message = 'Не найдено'): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], self::CODE_NOT_FOUND);
    }

    /**
     * Make error response.
     *
     * @param string $message
     * @param array|null $payload
     *
     * @return  JsonResponse
     */
    public static function error(string $message = 'Ошибка', ?array $payload = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'payload' => $payload,
        ], self::CODE_ERROR);
    }

    /**
     * Make token mismatch response.
     *
     * @param string $message
     * @param array|null $payload
     *
     * @return  JsonResponse
     */
    public static function tokenMismatch(string $message = 'Неверный токен', ?array $payload = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'payload' => $payload,
        ], self::CODE_TOKEN_MISMATCH);
    }

    /**
     * Make 200 response with data.
     *
     * @param mixed $data
     * @param mixed|null $payload
     * @param string|null $message
     * @param Carbon|null $lastModified
     *
     * @return  JsonResponse
     */
    public static function response($data, $payload = null, ?string $message = 'Успешно', ?Carbon $lastModified = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'payload' => $payload,
        ], self::CODE_OK, self::lastModHeaders($lastModified));
    }

    /**
     * Make 200 response with (base 64 encoded) file.
     *
     * @param string $content
     * @param string $filename
     * @param string $type
     * @param null $payload
     * @param Carbon|null $lastModified
     *
     * @return  JsonResponse
     */
    public static function file(string $content, string $filename, string $type, $payload = null, ?Carbon $lastModified = null): JsonResponse
    {
        return response()->json([
            'message' => 'OK',
            'content' => $content,
            'file_name' => $filename,
            'type' => $type,
            'payload' => $payload,
        ], self::CODE_OK, self::lastModHeaders($lastModified));
    }

    /**
     * Make list response.
     *
     * @param LengthAwarePaginator $list
     * @param array|null $titles
     * @param array|null $filters
     * @param array|null $defaultFilters
     * @param string|null $search
     * @param string|null $order
     * @param string|null $orderBy
     * @param array|null $ordering
     * @param array|null $payload
     * @param Carbon|null $lastModified
     *
     * @return  JsonResponse
     */
    public static function list(
        LengthAwarePaginator $list,
        ?array $titles = null,
        ?array $filters = null,
        ?array $defaultFilters = null,
        ?string $search = null,
        ?string $order = null,
        ?string $orderBy = null,
        ?array $ordering = null,
        ?array $payload = null,
        ?Carbon $lastModified = null
    ): JsonResponse
    {
        return response()->json([
            'message' => 'OK',
            'list' => $list->items(),
            'filters' => $filters,
            'default_filters' => $defaultFilters,
            'titles' => $titles,
            'search' => $search,
            'order' => $order,
            'order_by' => $orderBy,
            'ordering' => $ordering,
            'payload' => $payload,
            'pagination' => [
                'current_page' => $list->currentPage(),
                'last_page' => $list->lastPage(),
                'from' => $list->firstItem() ?? 0,
                'to' => $list->lastItem() ?? 0,
                'total' => $list->total(),
                'per_page' => $list->perPage(),
            ],
        ], self::CODE_OK, self::lastModHeaders($lastModified));
    }

    /**
     * Make 200 form response with data and payload.
     *
     * @param array $values
     * @param array $rules
     * @param array $titles
     * @param mixed $payload
     *
     * @return  JsonResponse
     */
    public static function form(array $values, array $rules, array $titles, array $payload = []): JsonResponse
    {
        return response()->json([
            'message' => 'OK',
            'values' => $values,
            'rules' => $rules,
            'titles' => $titles,
            'payload' => $payload,
        ], self::CODE_OK);
    }

    /**
     * Make 200 form response with data and payload.
     *
     * @param string $message
     * @param mixed $payload
     *
     * @return  JsonResponse
     */
    public static function success(string $message, array $payload = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'payload' => $payload,
        ], self::CODE_OK);
    }

    /**
     * Make 422 form validation error response.
     *
     * @param array $errors
     * @param mixed $payload
     * @param string $message
     *
     * @return  JsonResponse
     */
    public static function validationError(array $errors = [], string $message = 'Не все поля корректно заполнены', array $payload = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'payload' => $payload,
        ], self::CODE_VALIDATION_ERROR);
    }

    /**
     * Add last modifier header to response.
     * Modified timestamp must be GMT timezone.
     *
     * @param Carbon|null $lastMod
     * @param array $headers
     *
     * @return  array
     */
    private static function lastModHeaders(?Carbon $lastMod, array $headers = []): array
    {
        if ($lastMod === null) {
            return $headers;
        }

        return array_merge($headers, [
            'Last-Modified' => $lastMod->clone()->setTimezone('GMT')->format('D, d M Y H:i:s') . ' GMT',
        ]);
    }
}
