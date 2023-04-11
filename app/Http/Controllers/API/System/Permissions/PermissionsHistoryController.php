<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use App\Models\EntryScope;

class PermissionsHistoryController extends ApiHistoryController
{
    /**
     * Get permissions history list.
     *
     * @param APIListRequest $request
     *
     * @return  ApiResponse
     */
    public function list(APIListRequest $request): ApiResponse
    {
        $query = History::query()->where('entry_name', EntryScope::permission_group);

        $history = $this->retrieveHistory($query, $request);

        return $this->listResponse($history);
    }

    /**
     * Get permissions history record comments
     *
     * @param int $id
     *
     * @return  ApiResponse
     */
    public function comments(int $id): ApiResponse
    {
        /** @var History|null $record */
        $record = $this->retrieveRecord(History::query()->with('comments')->where('entry_name', EntryScope::permission_group), $id);

        if ($record === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($record->comments);
    }

    /**
     * Get permissions history record changes
     *
     * @param int $id
     *
     * @return  ApiResponse
     */
    public function changes(int $id): ApiResponse
    {
        /** @var History|null $record */
        $record = $this->retrieveRecord(History::query()->where('entry_name', EntryScope::permission_group), $id);

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return $this->changesResponse($record);
    }
}
