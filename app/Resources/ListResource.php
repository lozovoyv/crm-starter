<?php
declare(strict_types=1);

namespace App\Resources;

use App\Utils\Casting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class ListResource
{
    protected Builder $query;

    protected string $order;
    protected string $orderBy;
    protected array $filters;

    protected array $titles = [];
    protected array $orderableColumns = [];

    /**
     * Remember applied filters.
     *
     * @param array $filters
     *
     * @return $this
     */
    public function filter(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get applied filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Remember ordering column key and order direction.
     *
     * @param string $orderBy
     * @param string $order
     *
     * @return $this
     */
    public function order(string $orderBy, string $order): self
    {
        $this->orderBy = $orderBy;
        $this->order = $order;

        return $this;
    }

    /**
     * Get applied order column key.
     *
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * Get applied order direction.
     *
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * Set list titles.
     *
     * @param array $titles
     *
     * @return $this
     */
    public function setTitles(array $titles): self
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * Get list columns keys and titles.
     *
     * @return array
     */
    public function getTitles(): array
    {
        return array_map(static function (?string $title): ?string {
            return $title ? trans($title) : null;
        }, $this->titles);
    }

    /**
     * Set orderable columns.
     *
     * @param array $keys
     *
     * @return $this
     */
    public function setOrderableColumns(array $keys): self
    {
        $this->orderableColumns = $keys;

        return $this;
    }

    /**
     * Get list orderable columns keys.
     *
     * @return array
     */
    public function getOrderableColumns(): array
    {
        return $this->orderableColumns;
    }

    /**
     * Cast filters from string.
     *
     * @param array|null $filters
     * @param array $casting
     *
     * @return array
     */
    protected function castFilters(?array $filters, array $casting = []): array
    {
        if (empty($filters)) {
            $filters = [];
        }

        foreach ($casting as $key => $type) {
            if (isset($filters[$key])) {
                $filters[$key] = Casting::fromString($filters[$key], $type);
            } else {
                $filters[$key] = null;
            }
        }
        return $filters;
    }

    /**
     * Get paginated list.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function paginate(int $page, int $perPage): LengthAwarePaginator
    {
        return $this->query->paginate($perPage, ['*'], null, $page);
    }

    /**
     * Get list.
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->query->get();
    }
}
