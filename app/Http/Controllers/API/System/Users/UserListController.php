<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
use App\Resources\Users\UserListResource;

class UserListController extends ApiController
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__users, Permission::system__users_change),
        ]);
    }

    /**
     * Get users list.
     *
     * @param APIListRequest $request
     * @param UserListResource $resource
     *
     * @return  ApiResponse
     */
    public function __invoke(APIListRequest $request, UserListResource $resource): ApiResponse
    {
        $users = $resource
            ->filter($request->filters())
            ->search($request->search())
            ->order($request->orderBy(), $request->order())
            ->paginate($request->page(), $request->perPage());

        $users->transform(function (User $user) {
            return UserListResource::format($user);
        });

        return ApiResponse::list($users)
            ->titles($resource->getTitles())
            ->order($resource->getOrderBy(), $resource->getOrder())
            ->orderable($resource->getOrderableColumns());
    }
}
