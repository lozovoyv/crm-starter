<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use App\Models\EntryScope;
use Illuminate\Http\Request;

class RolesHistoryController extends ApiHistoryController
{
    /**
     * Get roles history list.
     *
     * @param APIListRequest $request
     *
     * @return  ApiResponse
     */
    public function list(APIListRequest $request): ApiResponse
    {
        $query = History::query()->where('entry_name', EntryScope::role);

        $history = $this->retrieveHistory($query, $request);

        return $this->listResponse($request, $history);
    }

    /**
     * Get role history record comments
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function comments(Request $request): ApiResponse
    {
        // TODO refactor on need !!!

        /** @var History|null $record */
        $record = History::query()
            ->with('comments')
            ->where('entry_name', EntryScope::role)
            ->where('id', $request->input('id'))
            ->first();

        if ($record === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($record->comments);
    }

    /**
     * Get role history record changes
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function changes(Request $request): ApiResponse
    {
        /** @var History|null $record */
        $record = $this->retrieveRecord(History::query()->where('entry_name', EntryScope::role), $request);

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return $this->changesResponse($record);
    }
}
