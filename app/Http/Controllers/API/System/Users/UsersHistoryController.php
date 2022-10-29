<?php

namespace App\Http\Controllers\API\System\Users;

use App\Http\APIResponse;
use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Models\History\History;
use App\Models\History\HistoryScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersHistoryController extends ApiHistoryController
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
        $query = History::query()->where('entry_name', HistoryScope::user);

        $history = $this->retrieveHistory($query, $request);

        return $this->listResponse($request, $history);
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
        // TODO refactor on need !!!

        /** @var History|null $record */
        $record = History::query()
            ->with('comments')
            ->where('entry_name', HistoryScope::user)
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
        $record = $this->retrieveRecord(History::query()->where('entry_name', HistoryScope::user), $request);

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return $this->changesResponse($record);
    }
}
