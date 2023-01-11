<?php

namespace App\Http\Controllers\API\System\Staff;

use App\Http\APIResponse;
use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Models\History\History;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffOperationsController extends ApiHistoryController
{
    /**
     * Get staff history list.
     *
     * @param APIListRequest $request
     * @param int $id
     *
     * @return  JsonResponse
     */
    public function list(APIListRequest $request, int $id): JsonResponse
    {
        $query = History::query()
            ->where('position_id', $id);

        $history = $this->retrieveHistory($query, $request);

        return $this->listResponse($request, $history);
    }

    /**
     * Get staff history record comments
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function comments(Request $request, int $id): JsonResponse
    {
        // TODO refactor on need !!!

        /** @var History|null $record */
        $record = History::query()
            ->with('comments')
            ->where('position_id', $id)
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
     * @param int $id
     *
     * @return JsonResponse
     */
    public function changes(Request $request, int $id): JsonResponse
    {
        $query = History::query()
            ->where('position_id', $id);

        /** @var History|null $record */
        $record = $this->retrieveRecord($query, $request);

        if ($record === null) {
            return APIResponse::error('Запись не найдена');
        }

        return $this->changesResponse($record);
    }
}
