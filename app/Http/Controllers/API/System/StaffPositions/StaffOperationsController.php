<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\StaffPositions;

use App\Http\Controllers\ApiHistoryController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\History\History;
use Illuminate\Http\Request;

class StaffOperationsController extends ApiHistoryController
{
    /**
     * Get staff history list.
     *
     * @param APIListRequest $request
     * @param int $id
     *
     * @return  ApiResponse
     */
    public function list(APIListRequest $request, int $id): ApiResponse
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
     * @return ApiResponse
     */
    public function comments(Request $request, int $id): ApiResponse
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

        return APIResponse::list()->items($record->comments);
    }

    /**
     * Get staff history record changes
     *
     * @param Request $request
     * @param int $id
     *
     * @return ApiResponse
     */
    public function changes(Request $request, int $id): ApiResponse
    {
        $query = History::query()
            ->where('position_id', $id);

        /** @var History|null $record */
        $record = $this->retrieveRecord($query, $request);

        if ($record === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return $this->changesResponse($record);
    }
}
