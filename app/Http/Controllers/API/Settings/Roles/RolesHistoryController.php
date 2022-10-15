<?php

namespace App\Http\Controllers\API\Settings\Roles;

use App\Http\APIResponse;
use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Models\History\History;
use App\Models\History\HistoryChanges;
use App\Models\History\HistoryScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolesHistoryController extends ApiHistoryController
{
    /**
     * Get roles history list.
     *
     * @param APIListRequest $request
     *
     * @return  JsonResponse
     */
    public function list(APIListRequest $request): JsonResponse
    {
        $query = History::query()->where('entry_name', HistoryScope::role);

        $history = $this->retrieveHistory($query, $request);

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
     * Get role history record comments
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function comments(Request $request): JsonResponse
    {
        /** @var History|null $record */
        $record = History::query()
            ->with('comments')
            ->where('entry_name', HistoryScope::role)
            ->where('id', $request->input('id'))
            ->first();

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return APIResponse::list($record->comments);
    }

    /**
     * Get role history record changes
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changes(Request $request): JsonResponse
    {
        /** @var History|null $record */
        $record = History::query()
            ->with('changes')
            ->where('entry_name', HistoryScope::role)
            ->where('id', $request->input('id'))
            ->first();

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        $changes = $record->changes->transform(function (HistoryChanges $changes) {
            switch ($changes->parameter) {
                case 'name': $changes->parameter = 'Название'; break;
                case 'active': $changes->parameter = 'Статус'; break;
                case 'description': $changes->parameter = 'Описание'; break;
                case 'permissions': $changes->parameter = 'Права'; break;
            }
            return $changes;
        });

        return APIResponse::list($changes, ['Параметр', 'Старое значение', 'Новое значение']);
    }
}
