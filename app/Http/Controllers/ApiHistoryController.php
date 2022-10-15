<?php

namespace App\Http\Controllers;

use App\Http\Requests\APIListRequest;
use Illuminate\Database\Eloquent\Builder;
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

    protected function retrieveHistory(Builder $query, APIListRequest $request): LengthAwarePaginator
    {
        // apply order
        $this->order = $request->order();
        $this->orderBy = $request->orderBy('created_at');
        switch ($this->orderBy) {
            case 'timestamp':
                $query->orderBy('timestamp', $this->order);
                break;
            default:
                $query->orderBy('id', $this->order);
        }

        $query->withCount(['comments', 'links', 'changes']);

        // apply filters
        $this->filters = $request->filters($this->defaultFilters);
        if (isset($this->filters['action_ids'])) {
            $query->whereIn('action_id', $this->filters['action_ids']);
        }

        return $request->paginate($query);
    }
}
