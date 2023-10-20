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

use App\Actions\Permission\PermissionGroupRemoveAction;
use App\Current;
use App\Exceptions\Model\ModelException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Resources\Permissions\PermissionGroupResource;

class PermissionGroupRemoveController extends ApiController
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__permissions),
        ]);
    }

    /**
     * Delete permission group.
     *
     * @param APIRequest $request
     * @param int $groupID
     *
     * @return  ApiResponse
     */
    public function __invoke(APIRequest $request, int $groupID): ApiResponse
    {
        $current = Current::init($request);

        $action = new PermissionGroupRemoveAction($current);

        try {
            $resource = PermissionGroupResource::get($groupID, $request->hash(), true);

            $action->execute($resource->group());

        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return ApiResponse::success('Учётная запись удалена');
    }
}
