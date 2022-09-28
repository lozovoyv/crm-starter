<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class APIListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Weather initial list request.
     *
     * @return  bool
     */
    public function isInitial(): bool
    {
        return (bool)$this->input('initial', false);
    }

    /**
     * Get filters list.
     *
     * @param array $default
     *
     * @return  array
     */
    public function filters(array $default = []): array
    {
        $filters = $this->input('filters', []);

        foreach ($default as $key => $filter) {
            if (!isset($filters[$key])) {
                $filters[$key] = $filter;
            }
        }

        return $filters;
    }

    /**
     * Get search terms.
     *
     * @param bool $raw
     *
     * @return  array|string|null
     */
    public function search(bool $raw = false)
    {
        $search = $this->input('search');

        if ($raw) {
            return $search;
        }

        if (empty($search)) {
            return [];
        }

        $search = explode(' ', $search);

        return array_map(static function ($term) {
            return trim($term);
        }, $search);
    }

    /**
     * Get search terms.
     *
     * @param string $default
     *
     * @return  string|null
     */
    public function order(string $default = 'asc'): ?string
    {
        $order = strtolower($this->input('order'));

        return in_array($order, ['asc', 'desc']) ? $order : $default;
    }

    /**
     * Get order by parameter.
     *
     * @param string|null $default
     *
     * @return  string|null
     */
    public function orderBy(?string $default = null): ?string
    {
        return $this->input('order_by', $default) ?? $default;
    }

    /**
     * Get requested page.
     *
     * @param string $key
     *
     * @return  int
     */
    public function page(string $key = 'page'): int
    {
        return $this->input($key, 1);
    }

    /**
     * Get requested number of items page.
     *
     * @param int $default
     * @param string $key
     *
     * @return  int
     */
    public function perPage(int $default = 10, string $key = 'per_page'): int
    {
        return $this->input($key, $default);
    }

    /**
     * Paginate query with request parameters.
     *
     * @param Builder $query
     * @param string $pageKey
     * @param string $perPageKey
     * @param int $perPageDefault
     *
     * @return  LengthAwarePaginator
     */
    public function paginate(Builder $query, string $pageKey = 'page', string $perPageKey = 'per_page', int $perPageDefault = 10): LengthAwarePaginator
    {
        return $query->paginate($this->perPage($perPageDefault, $perPageKey), ['*'], $pageKey, $this->page($pageKey));
    }
}
