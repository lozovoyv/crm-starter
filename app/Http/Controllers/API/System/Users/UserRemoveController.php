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

namespace App\Http\Controllers\API\System\Users;

use App\Actions\Users\UserRemoveAction;
use App\Current;
use App\Exceptions\Model\ModelException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Resources\Users\UserResource;

class UserRemoveController extends ApiController
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
     * Delete user.
     *
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function __invoke(APIRequest $request, int $userID): ApiResponse
    {
        $current = Current::init($request);

        $action = new UserRemoveAction($current);

        try {
            $resource = UserResource::init($userID, $request->hash(), true);

            if ($current->userId() === $resource->user()->id) {
                return APIResponse::error('Вы не можете удалить собственную учётную запись');
            }
            $action->execute($resource->user());
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return ApiResponse::success('Учётная запись удалена');
    }
}
