<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Resources\Users\UserHistoryResource;

class UserHistoryController extends ApiController
{
    /**
     * All users history list.
     *
     * @param APIListRequest $request
     * @param UserHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function list(APIListRequest $request, UserHistoryResource $resource): ApiResponse
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
     * All users history entry comments.
     *
     * @param int $historyID
     * @param UserHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function comments(int $historyID, UserHistoryResource $resource): ApiResponse
    {
        $history = $resource->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * All users history entry changes.
     *
     * @param int $historyID
     * @param UserHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function changes(int $historyID, UserHistoryResource $resource): ApiResponse
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
     * Certain user history list.
     *
     * @param int $userID
     * @param APIListRequest $request
     * @param UserHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function listForUser(int $userID, APIListRequest $request, UserHistoryResource $resource): ApiResponse
    {
        $history = $resource
            ->forEntry($userID)
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->order())
            ->paginate($request->page(), $request->perPage());

        return ApiResponse::list($history)
            ->titles($resource->getTitles())
            ->order($resource->getOrderBy(), $resource->getOrder())
            ->orderable($resource->getOrderableColumns());
    }

    /**
     * Certain user history entry comments.
     *
     * @param int $userID
     * @param int $historyID
     * @param UserHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function commentsForUser(int $userID, int $historyID, UserHistoryResource $resource): ApiResponse
    {
        $history = $resource->forEntry($userID)->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return APIResponse::list()->items($history->comments);
    }

    /**
     * Certain user history entry changes.
     *
     * @param int $userID
     * @param int $historyID
     * @param UserHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function changesForUser(int $userID, int $historyID, UserHistoryResource $resource): ApiResponse
    {
        $history = $resource->forEntry($userID)->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list()
            ->items($history->getChanges())
            ->titles($resource->getChangesTitles());
    }
}
