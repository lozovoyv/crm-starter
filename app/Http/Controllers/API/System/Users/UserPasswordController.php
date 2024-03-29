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

use App\Actions\Users\UserPasswordChangeAction;
use App\Current;
use App\Exceptions\Model\ModelException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Resources\Users\UserResource;
use App\VDTO\UserVDTO;

class UserPasswordController extends ApiController
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
     * Change user password.
     *
     * @param APIRequest $request
     * @param int $userID
     *
     * @return  ApiResponse
     */
    public function __invoke(APIRequest $request, int $userID): ApiResponse
    {

        try {
            $resource = UserResource::get($userID, $request->hash(), true);
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $vdto = new UserVDTO($request->data(['new_password', 'clear_password']));

        if ($errors = $vdto->validate(['new_password', 'clear_password'], $resource->user())) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);

        $action = new UserPasswordChangeAction($current);
        $action->execute($resource->user(), $vdto);

        return APIResponse::success('Пароль сохранён');
    }
}
