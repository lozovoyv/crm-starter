<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use App\Models\EntryScope;
use Illuminate\Http\Request;

class UsersHistoryController extends ApiHistoryController
{
    /**
     * Get roles history list.
     *
     * @param APIListRequest $request
     *
     * @return  APIResponse
     */
    public function list(APIListRequest $request): APIResponse
    {
        $query = History::query()->where('entry_name', EntryScope::user);

        $history = $this->retrieveHistory($query, $request);

        return $this->listResponse($request, $history);
    }

    /**
     * Get role history record comments
     *
     * @param Request $request
     *
     * @return APIResponse
     */
    public function comments(Request $request): APIResponse
    {
        // TODO refactor on need !!!

        /** @var History|null $record */
        $record = History::query()
            ->with('comments')
            ->where('entry_name', EntryScope::user)
            ->where('id', $request->input('id'))
            ->first();

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return ApiResponse::list()->items($record->comments);
    }

    /**
     * Get role history record changes
     *
     * @param Request $request
     *
     * @return  APIResponse
     */
    public function changes(Request $request): APIResponse
    {
        /** @var History|null $record */
        $record = $this->retrieveRecord(History::query()->where('entry_name', EntryScope::user), $request);

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return $this->changesResponse($record);
    }
}
