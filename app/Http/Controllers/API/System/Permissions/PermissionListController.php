<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Resources\Permissions\PermissionListResource;

class PermissionListController extends ApiController
{
    /**
     * Permissions list.
     *
     * @param APIListRequest $request
     * @param PermissionListResource $resource
     *
     * @return  ApiResponse
     */
    public function list(APIListRequest $request, PermissionListResource $resource): ApiResponse
    {
        $permissions = $resource
            ->filter($request->filters())
            ->search($request->search())
            ->order($request->orderBy('order'), $request->order())
            ->get();

        $permissions->transform(function (Permission $permission) {
            return PermissionListResource::format($permission);
        });

        return ApiResponse::list($permissions)
            ->titles($resource->getTitles())
            ->order($resource->getOrderBy(), $resource->getOrder())
            ->orderable($resource->getOrderableColumns());
    }
}
