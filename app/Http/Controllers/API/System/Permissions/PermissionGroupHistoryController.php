<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Resources\Permissions\PermissionGroupsHistoryResource;

class PermissionGroupHistoryController extends ApiController
{
    /**
     * All users history list.
     *
     * @param APIListRequest $request
     * @param PermissionGroupsHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function list(APIListRequest $request, PermissionGroupsHistoryResource $resource): ApiResponse
    {
        $list = $resource
            ->filter($request->filters())
            ->order($request->orderBy('timestamp'), $request->orderDirection())
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
     * @param PermissionGroupsHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function comments(int $historyID, PermissionGroupsHistoryResource $resource): ApiResponse
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
     * @param PermissionGroupsHistoryResource $resource
     *
     * @return ApiResponse
     */
    public function changes(int $historyID, PermissionGroupsHistoryResource $resource): ApiResponse
    {
        $history = $resource->retrieveRecord($historyID);

        if ($history === null) {
            return ApiResponse::error('Запись не найдена');
        }

        return ApiResponse::list()
            ->items($history->getChanges())
            ->titles($resource->getChangesTitles());
    }
}
