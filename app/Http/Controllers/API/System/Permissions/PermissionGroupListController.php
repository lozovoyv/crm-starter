<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\PermissionGroup;
use App\Resources\Permissions\PermissionGroupsListResource;

class PermissionGroupListController extends ApiController
{
    /**
     * Get permission groups list.
     *
     * @param APIListRequest $request
     * @param PermissionGroupsListResource $resource
     *
     * @return  ApiResponse
     */
    public function list(APIListRequest $request, PermissionGroupsListResource $resource): ApiResponse
    {
        $groups = $resource
            ->filter($request->filters())
            ->search($request->search())
            ->order($request->orderBy('id'), $request->order())
            ->paginate($request->page(), $request->perPage());

        $groups->transform(function (PermissionGroup $permissionGroup) {
            return PermissionGroupsListResource::format($permissionGroup);
        });

        return ApiResponse::list($groups)
            ->titles($resource->getTitles())
            ->order($resource->getOrderBy(), $resource->getOrder())
            ->orderable($resource->getOrderableColumns());
    }
}
