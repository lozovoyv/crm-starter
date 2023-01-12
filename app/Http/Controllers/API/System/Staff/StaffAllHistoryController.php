<?php

namespace App\Http\Controllers\API\System\Staff;

use App\Http\APIResponse;
use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Models\History\History;
use App\Models\EntryScope;
use App\Models\Positions\PositionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffAllHistoryController extends ApiHistoryController
{
    /**
     * Get staff history list.
     *
     * @param APIListRequest $request
     *
     * @return  JsonResponse
     */
    public function list(APIListRequest $request): JsonResponse
    {
        $query = History::query()
            ->where('entry_name', EntryScope::position)
            ->where('entry_type', PositionType::typeToString(PositionType::staff));

        $history = $this->retrieveHistory($query, $request);

        return $this->listResponse($request, $history);
    }

    /**
     * Get staff history record comments
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
            ->where('entry_name', EntryScope::position)
            ->where('entry_type', PositionType::typeToString(PositionType::staff))
            ->where('id', $request->input('id'))
            ->first();

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return APIResponse::list($record->comments);
    }

    /**
     * Get staff history record changes
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changes(Request $request): JsonResponse
    {
        $query = History::query()
            ->where('entry_name', EntryScope::position)
            ->where('entry_type', PositionType::typeToString(PositionType::staff));

        /** @var History|null $record */
        $record = $this->retrieveRecord($query, $request);

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return $this->changesResponse($record);
    }
}