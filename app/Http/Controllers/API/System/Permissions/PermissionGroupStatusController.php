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

use App\Actions\Permission\PermissionGroupStatusChangeAction;
use App\Actions\Users\UserStatusChangeAction;
use App\Current;
use App\Exceptions\Model\ModelException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Models\Users\UserStatus;
use App\Resources\Permissions\PermissionGroupResource;
use App\Resources\Users\UserResource;
use App\VDTO\PermissionGroupVDTO;
use App\VDTO\UserVDTO;

class PermissionGroupStatusController extends ApiController
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
     * Change permission group status.
     *
     * @param APIRequest $request
     * @param int $groupID
     *
     * @return  ApiResponse
     */
    public function __invoke(int $groupID, APIRequest $request): ApiResponse
    {
        try {
            $resource = PermissionGroupResource::get($groupID, $request->hash(), true);
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $vdto = new PermissionGroupVDTO($request->data(['active']));

        if ($errors = $vdto->validate(['active'], $resource->group())) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);

        $action = new PermissionGroupStatusChangeAction($current);
        $action->execute($resource->group(), $vdto);

        return APIResponse::success($resource->group()->active ? 'Группа прав включена' : 'Группа прав отключена');
    }
}
