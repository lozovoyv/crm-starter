<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use App\Utils\Casting;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;

abstract class ApiHistoryController extends Controller
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
     * Builder for history query. Must be redefined.
     * Router params would be passed to `getQuery()` target method in order of appearance.
     * Notice: all params type is string.
     *
     * @param array $with
     * @param array $args
     *
     * @return Builder
     */
    protected function getQueryForHistory(array $with, array $args): Builder
    {
        if (!method_exists($this, 'getQuery')) {
            throw new BadMethodCallException('getQuery() method must be defined in ' . static::class);
        }

        return $this->getQuery($with, ...$args);
    }

    /**
     * Extract historyID from router params.
     * Notice: Route parameters are injected into controllers based on their order - the names do not matter.
     *
     * @param array $args
     *
     * @return int|null
     */
    protected function getArgumentForHistoryID(array $args): ?int
    {
        if (method_exists($this, 'getHistoryID')) {
            return $this->getHistoryID($args);
        }

        return isset($args[0]) ? (int)$args[0] : null;
    }

    /**
     * Extract arguments except historyID from router params.
     * Notice: Route parameters are injected into controllers based on their order - the names do not matter.
     *
     * @param array $args
     *
     * @return array
     */
    protected function getArgumentForQuery(array $args): array
    {
        if (method_exists($this, 'getArguments')) {
            return $this->getArguments($args);
        }

        array_shift($args);
        return $args;
    }

    /**
     * Get permissions history list.
     *
     * @param APIListRequest $request
     * @param mixed ...$args
     *
     * @return  ApiResponse
     */
    public function list(APIListRequest $request, ...$args): ApiResponse
    {
        $query = $this->getQueryForHistory([], $args);

        $history = $this->retrieveHistory($query, $request);

        return $this->listResponse($history);
    }

    /**
     * Get history record comments
     *
     * @param string[] ...$args
     *
     * @return  ApiResponse
     */
    public function comments(...$args): ApiResponse
    {
        /** @var History|null $record */
        $record = $this->retrieveRecord($this->getQueryForHistory(['comments'], $this->getArgumentForQuery($args)), $this->getArgumentForHistoryID($args));

        if ($record === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($record->comments);
    }

    /**
     * Get history record changes
     *
     * @param string[] ...$args
     *
     * @return  ApiResponse
     */
    public function changes(...$args): ApiResponse
    {
        /** @var History|null $record */
        $record = $this->retrieveRecord($this->getQueryForHistory([], $this->getArgumentForQuery($args)), $this->getArgumentForHistoryID($args));

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return $this->changesResponse($record);
    }

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
        $this->order = $request->order('desc');
        // fixed ordering by timestamp only
        $this->orderBy = 'timestamp';
        $query->orderBy('timestamp', $this->order)->orderBy('id', $this->order);

        $query->withCount(['comments', 'links', 'changes']);

        // apply filters
        $this->filters = $request->filters(['action_ids' => Casting::array]);
        if (isset($this->filters['action_ids'])) {
            $query->whereIn('action_id', $this->filters['action_ids']);
        }

        return $request->paginate($query);
    }

    /**
     * History list response.
     *
     * @param LengthAwarePaginator $history
     *
     * @return APIResponse
     */
    protected function listResponse(LengthAwarePaginator $history): APIResponse
    {
        return APIResponse::list()
            ->items($history)
            ->titles($this->titles)
            ->order($this->orderBy, $this->order)
            ->orderable($this->ordering);
    }

    /**
     * Retrieve history record.
     *
     * @param Builder $query
     * @param int $id
     *
     * @return History|null
     */
    protected function retrieveRecord(Builder $query, int $id): ?History
    {
        /** @var History|null $record */
        $record = $query->with(['changes', 'comments'])
            ->where('id', $id)
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
