<?php

namespace App\Http;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class APIResponse
{
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
        ], 301);
    }

    /**
     * Make 304 not modified response.
     *
     * @return  JsonResponse
     */
    public static function notModified(): JsonResponse
    {
        return response()->json(null, 304);
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
        ], 403);
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
        ], 404);
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
        ], 406);
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
    public static function response(mixed $data, mixed $payload = null, ?string $message = 'Успешно', ?Carbon $lastModified = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'payload' => $payload,
        ], 200, self::lastModHeaders($lastModified));
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
        ], 200, self::lastModHeaders($lastModified));
    }

    /**
     * Make list response.
     *
     * @param LengthAwarePaginator $list
     * @param array|null $titles
     * @param array|null $filters
     * @param array|null $defaultFilters
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
            'payload' => $payload,
            'pagination' => [
                'current_page' => $list->currentPage(),
                'last_page' => $list->lastPage(),
                'from' => $list->firstItem() ?? 0,
                'to' => $list->lastItem() ?? 0,
                'total' => $list->total(),
                'per_page' => $list->perPage(),
            ],
        ], 200, self::lastModHeaders($lastModified));
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
        ], 200);
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
        ], 200);
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
        ], 422);
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
