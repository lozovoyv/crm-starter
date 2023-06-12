<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Staff;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Resources\History\CommonHistoryResource;
use App\Resources\Staff\StaffHistoryResource;

class StaffHistoryController extends ApiController
{
    /**
     * All staff history list.
     *
     * @param APIListRequest $request
     * @param StaffHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function list(APIListRequest $request, StaffHistoryResource $resource): ApiResponse
    {
        $list = $resource
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->order())
            ->paginate($request->page(), $request->perPage());

        return ApiResponse::list($list)
            ->titles($resource->getTitles())
            ->order($resource->getOrderBy(), $resource->getOrder())
            ->orderable($resource->getOrderableColumns());
    }

    /**
     * All staff history entry comments.
     *
     * @param int $historyID
     * @param StaffHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function comments(int $historyID, StaffHistoryResource $resource): ApiResponse
    {
        $history = $resource->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * All staff history entry changes.
     *
     * @param int $historyID
     * @param StaffHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function changes(int $historyID, StaffHistoryResource $resource): ApiResponse
    {
        $history = $resource->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list()
            ->items($history->getChanges())
            ->titles($resource->getChangesTitles());
    }

    /**
     * Certain staff history list.
     *
     * @param int $positionID
     * @param APIListRequest $request
     * @param StaffHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function listForStaff(int $positionID, APIListRequest $request, StaffHistoryResource $resource): ApiResponse
    {
        $history = $resource
            ->forEntry($positionID)
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->order())
            ->paginate($request->page(), $request->perPage());

        return ApiResponse::list($history)
            ->titles($resource->getTitles())
            ->order($resource->getOrderBy(), $resource->getOrder())
            ->orderable($resource->getOrderableColumns());
    }

    /**
     * Certain staff history entry comments.
     *
     * @param int $positionID
     * @param int $historyID
     * @param StaffHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function commentsForStaff(int $positionID, int $historyID, StaffHistoryResource $resource): ApiResponse
    {
        $history = $resource->forEntry($positionID)->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Certain staff history entry changes.
     *
     * @param int $positionID
     * @param int $historyID
     * @param StaffHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function changesForStaff(int $positionID, int $historyID, StaffHistoryResource $resource): ApiResponse
    {
        $history = $resource->forEntry($positionID)->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list()
            ->items($history->getChanges())
            ->titles($resource->getChangesTitles());
    }

    /**
     * Certain staff operations history list.
     *
     * @param int $positionID
     * @param APIListRequest $request
     * @param CommonHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function listByStaff(int $positionID, APIListRequest $request, CommonHistoryResource $resource): ApiResponse
    {
        $history = $resource
            ->forOperator($positionID)
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->order())
            ->paginate($request->page(), $request->perPage());

        return ApiResponse::list($history)
            ->titles($resource->getTitles())
            ->order($resource->getOrderBy(), $resource->getOrder())
            ->orderable($resource->getOrderableColumns());
    }

    /**
     * Certain staff operations history entry comments.
     *
     * @param int $positionID
     * @param int $historyID
     * @param CommonHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function commentsByStaff(int $positionID, int $historyID, CommonHistoryResource $resource): ApiResponse
    {
        $history = $resource->forOperator($positionID)->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Certain staff operations history entry changes.
     *
     * @param int $positionID
     * @param int $historyID
     * @param CommonHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function changesByStaff(int $positionID, int $historyID, CommonHistoryResource $resource): ApiResponse
    {
        $history = $resource->forOperator($positionID)->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list()
            ->items($history->getChanges())
            ->titles($resource->getChangesTitles());
    }
}
