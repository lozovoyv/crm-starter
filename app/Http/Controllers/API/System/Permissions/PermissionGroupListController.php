<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Models\Positions\PositionType;
use App\Utils\Translate;

class PermissionGroupListController extends ApiController
{
    protected array $titles = [
        'id' => 'permissions/permission_group.id',
        'state' => null,
        'name' => 'permissions/permission_group.name',
        'count' => 'permissions/permission_group.count',
        'description' => 'permissions/permission_group.description',
        'created_at' => 'permissions/permission_group.created_at',
        'updated_at' => 'permissions/permission_group.updated_at',
    ];

    protected array $orderableColumns = ['id', 'name', 'created_at', 'updated_at'];

    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__permissions),
        ]);
    }

    /**
     * Get permission groups list.
     *
     * @param APIListRequest $request
     *
     * @return  ApiResponse
     */
    public function __invoke(APIListRequest $request): ApiResponse
    {
        $list = PermissionGroup::query()
            ->withCountPermissions()
            ->filter($request->filters())
            ->search($request->search())
            ->order($request->orderBy('id'), $request->orderDirection('asc'))
            ->pagination($request->page(), $request->perPage());

        $list->transform(function (PermissionGroup $permissionGroup) {
            return [
                'id' => $permissionGroup->id,
                'name' => $permissionGroup->name,
                'count' => $permissionGroup->getAttribute('permissions_count'),
                'description' => $permissionGroup->description,
                'active' => $permissionGroup->active,
                'locked' => $permissionGroup->locked,
                'created_at' => $permissionGroup->created_at,
                'updated_at' => $permissionGroup->updated_at,
                'hash' => $permissionGroup->hash(),
            ];
        });

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }
}
