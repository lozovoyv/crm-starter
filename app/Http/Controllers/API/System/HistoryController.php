<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System;

use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use App\Models\EntryScope;
use Illuminate\Http\Request;

class HistoryController extends ApiHistoryController
{
    /**
     * Get history list.
     *
     * @param APIListRequest $request
     *
     * @return  APIResponse
     */
    public function list(APIListRequest $request): APIResponse
    {
        $history = $this->retrieveHistory(History::query(), $request);

        return $this->listResponse($history);
    }

    /**
     * Get history record comments
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
            ->where('entry_name', EntryScope::permission_group)
            ->where('id', $request->input('id'))
            ->first();

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return APIResponse::list()
            ->items($record->comments);
    }

    /**
     * Get history record changes
     *
     * @param Request $request
     *
     * @return APIResponse
     */
    public function changes(Request $request): APIResponse
    {
        /** @var History|null $record */
        $record = $this->retrieveRecord(History::query(), $request);

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return $this->changesResponse($record);
    }
}
