<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;

class ApiHistoryController extends Controller
{
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
        // fixed ordering by timestamp only
        $this->orderBy = 'timestamp';
        $query->orderBy('timestamp', $this->order)->orderBy('id', $this->order);

        $query->withCount(['comments', 'links', 'changes']);

        // apply filters
        $this->filters = $request->filters();
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
     * @return APIResponse
     */
    protected function listResponse(APIListRequest $request, LengthAwarePaginator $history): APIResponse
    {
        return APIResponse::list()
            ->items($history)
            ->titles($this->titles)
            ->filters($this->filters)
            ->search($request->search(true))
            ->order($this->orderBy, $this->order)
            ->orderable($this->ordering);
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
     * @return ApiResponse
     */
    protected function changesResponse(History $record): ApiResponse
    {
        return ApiResponse::list()
            ->items($record->getChanges())
            ->titles(['Параметр', 'Старое значение', 'Новое значение']);
    }
}
