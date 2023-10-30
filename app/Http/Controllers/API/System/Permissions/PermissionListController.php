<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Http\Controllers\API\System\Permissions;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Utils\Translate;

class PermissionListController extends ApiController
{
    protected array $titles = [
        'scope_name' =>'permissions/permission.scope',
        'name' => 'permissions/permission.name',
        'description' => 'permissions/permission.description',
        'key' => 'permissions/permission.key',
    ];

    protected array $orderableColumns = ['scope_name', 'name', 'key'];

    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__permissions),
        ]);
    }

    /**
     * Get permissions list.
     *
     * @param APIListRequest $request
     *
     * @return  ApiResponse
     */
    public function __invoke(APIListRequest $request): ApiResponse
    {
        $list = Permission::query()
            ->withScope()
            ->filter($request->filters())
            ->search($request->search())
            ->order($request->orderBy('scope_name'), $request->orderDirection('asc'))
            ->get();

        $list->transform(function (Permission $permission) {
            return [
                'id' => $permission->id,
                'key' => $permission->key,
                'scope' => $permission->scope->name,
                'name' => $permission->name,
                'description' => $permission->description,
            ];
        });

        return ApiResponse::list($list)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }
}
