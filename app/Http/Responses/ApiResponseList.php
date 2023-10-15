<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ApiResponseList extends ApiResponse
{
    protected int $statusCode = self::CODE_OK;

    protected array|Arrayable|Collection|LengthAwarePaginator $list = [];
    protected ?array $filters;
    protected ?string $title;
    protected ?array $titles;
    protected ?string $search;
    protected ?string $orderDirection;
    protected ?string $orderBy;
    protected ?array $orderable;

    /**
     * Get response.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     *
     * @throws InvalidArgumentException
     */
    public function toResponse($request): JsonResponse
    {
        if ($this->list instanceof LengthAwarePaginator) {
            $list = array_values($this->list->items());
            $pagination = [
                'current_page' => $this->list->currentPage(),
                'last_page' => $this->list->lastPage(),
                'from' => $this->list->firstItem(),
                'to' => $this->list->lastItem(),
                'total' => $this->list->total(),
                'per_page' => $this->list->perPage(),
            ];
        } else if ($this->list instanceof Arrayable) {
            $list = array_values($this->list->toArray());
            $pagination = null;
        } else if (is_array($this->list)) {
            $list = $this->list;
            $pagination = null;
        }

        return new JsonResponse(
            static::combine([
                'list' => $list,
            ], [
                'pagination' => $pagination ?? null,
                'title' => $this->title ?? null,
                'titles' => $this->titles ?? null,
                'filters' => $this->filters ?? null,
                'search' => $this->search ?? null,
                'order_by' => $this->orderBy ?? null,
                'order' => $this->orderDirection ?? null,
                'orderable' => $this->orderable ?? null,
                'message' => $this->message ?? null,
                'payload' => $this->payload ?? null,
            ]), $this->statusCode, $this->getHeaders()
        );
    }

    /**
     * List items.
     *
     * @param array|Arrayable|Collection|LengthAwarePaginator $list
     *
     * @return  $this
     */
    public function items(array|Arrayable|Collection|LengthAwarePaginator $list = []): ApiResponseList
    {
        $this->list = $list;

        return $this;
    }

    /**
     * List title.
     *
     * @param string|null $title
     *
     * @return  $this
     */
    public function title(?string $title): ApiResponseList
    {
        $this->title = $title;

        return $this;
    }

    /**
     * List column titles.
     *
     * @param array|null $titles
     *
     * @return  $this
     */
    public function titles(?array $titles): ApiResponseList
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * List current filters.
     *
     * @param array|null $filters
     *
     * @return  $this
     */
    public function filters(?array $filters): ApiResponseList
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * List applied search.
     *
     * @param string|null $search
     *
     * @return  $this
     */
    public function search(?string $search): ApiResponseList
    {
        $this->search = $search;

        return $this;
    }

    /**
     * List applied order.
     *
     * @param string|null $orderBy
     * @param string|null $orderDirection
     *
     * @return  $this
     */
    public function order(?string $orderBy, ?string $orderDirection): ApiResponseList
    {
        $this->orderDirection = $orderDirection;
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * List ordering options.
     *
     * @param array|null $orderable
     *
     * @return  $this
     */
    public function orderable(?array $orderable): ApiResponseList
    {
        $this->orderable = $orderable;

        return $this;
    }
}
