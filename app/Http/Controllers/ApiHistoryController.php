<?php

namespace App\Http\Controllers;

use App\Http\APIResponse;
use App\Http\Requests\APIListRequest;
use App\Models\History\History;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;

class ApiHistoryController extends BaseController
{
    protected array $defaultFilters = [
        'action_ids' => null,
    ];

    protected array $titles = [
        'timestamp' => 'Дата',
        'action' => 'Действие',
        'links' => 'Ссылки',
        'comment' => 'Комментарии',
        'changes' => 'Изменения',
        'position_id' => 'Оператор',
    ];

    protected array $ordering = ['timestamp'];

    protected ?array $filters;

    protected ?string $order;

    protected ?string $orderBy;

    /**
     * Retrieve history record list.
     *
     * @param Builder $query
     * @param APIListRequest $request
     *
     * @return LengthAwarePaginator
     */
    protected function retrieveHistory(Builder $query, APIListRequest $request): LengthAwarePaginator
    {
        // apply order
        $this->order = $request->order();
        $this->orderBy = $request->orderBy('created_at');
        switch ($this->orderBy) {
            case 'timestamp':
            default:
                $this->orderBy = 'timestamp';
                $query->orderBy('timestamp', $this->order)->orderBy('id', $this->order);
        }

        $query->withCount(['comments', 'links', 'changes']);

        // apply filters
        $this->filters = $request->filters($this->defaultFilters);
        if (isset($this->filters['action_ids'])) {
            $query->whereIn('action_id', $this->filters['action_ids']);
        }

        return $request->paginate($query);
    }

    /**
     * History list response.
     *
     * @param APIListRequest $request
     * @param LengthAwarePaginator $history
     *
     * @return JsonResponse
     */
    protected function listResponse(APIListRequest $request, LengthAwarePaginator $history): JsonResponse
    {
        return APIResponse::list(
            $history,
            $this->titles,
            $this->filters,
            $this->defaultFilters,
            $request->search(true),
            $this->order,
            $this->orderBy,
            $this->ordering
        );
    }

    /**
     * Retrieve history record.
     *
     * @param Builder $query
     * @param Request $request
     *
     * @return History|null
     */
    protected function retrieveRecord(Builder $query, Request $request): ?History
    {
        /** @var History|null $record */
        $record = $query->with(['changes', 'comments'])
            ->where('id', $request->input('id'))
            ->first();

        return $record;
    }

    /**
     * History changes response.
     *
     * @param History $record
     *
     * @return JsonResponse
     */
    protected function changesResponse(History $record): JsonResponse
    {
        return APIResponse::list($record->getChanges(), ['Параметр', 'Старое значение', 'Новое значение']);
    }
}
