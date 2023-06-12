<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get requested page.
     *
     * @param string $key
     *
     * @return  int
     */
    public function page(string $key = 'page'): int
    {
        return $this->integer($key, 1);
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
        return (int)$this->input($key, $default);
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
        return $this->input('filters', $default);
    }

    /**
     * Get search terms.
     *
     * @return string|null
     */
    public function search(): ?string
    {
        return $this->input('search');
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
        $order = strtolower($this->input('order') ?? $default);

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
}
