<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Admins;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Resources\Admins\AdminHistoryResource;
use App\Resources\History\CommonHistoryResource;

class AdminHistoryController extends ApiController
{
    /**
     * All admin history list.
     *
     * @param APIListRequest $request
     * @param AdminHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function list(APIListRequest $request, AdminHistoryResource $resource): ApiResponse
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
     * All admin history entry comments.
     *
     * @param int $historyID
     * @param AdminHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function comments(int $historyID, AdminHistoryResource $resource): ApiResponse
    {
        $history = $resource->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * All admin history entry changes.
     *
     * @param int $historyID
     * @param AdminHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function changes(int $historyID, AdminHistoryResource $resource): ApiResponse
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
     * Certain admin history list.
     *
     * @param int $positionID
     * @param APIListRequest $request
     * @param AdminHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function listForAdmin(int $positionID, APIListRequest $request, AdminHistoryResource $resource): ApiResponse
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
     * Certain admin history entry comments.
     *
     * @param int $positionID
     * @param int $historyID
     * @param AdminHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function commentsForAdmin(int $positionID, int $historyID, AdminHistoryResource $resource): ApiResponse
    {
        $history = $resource->forEntry($positionID)->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Certain admin history entry changes.
     *
     * @param int $positionID
     * @param int $historyID
     * @param AdminHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function changesForAdmin(int $positionID, int $historyID, AdminHistoryResource $resource): ApiResponse
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
     * Certain admin operations history list.
     *
     * @param int $positionID
     * @param APIListRequest $request
     * @param CommonHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function listByAdmin(int $positionID, APIListRequest $request, CommonHistoryResource $resource): ApiResponse
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
     * Certain admin operations history entry comments.
     *
     * @param int $positionID
     * @param int $historyID
     * @param CommonHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function commentsByAdmin(int $positionID, int $historyID, CommonHistoryResource $resource): ApiResponse
    {
        $history = $resource->forOperator($positionID)->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Certain admin operations history entry changes.
     *
     * @param int $positionID
     * @param int $historyID
     * @param CommonHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function changesByAdmin(int $positionID, int $historyID, CommonHistoryResource $resource): ApiResponse
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
