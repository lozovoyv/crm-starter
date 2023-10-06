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

use App\Actions\Users\UserEmailChangeAction;
use App\Current;
use App\Exceptions\Model\ModelException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Resources\Users\UserResource;
use App\VDTO\UserVDTO;

class UserEmailController extends ApiController
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
            $resource = UserResource::init($userID, $request->hash(), true);
        } catch (ModelException $exception) {
            return ApiResponse::error($exception->getMessage());
        }

        $vdto = new UserVDTO($request->data(['email', 'email_confirmation_need']));

        if ($errors = $vdto->validate(['email', 'email_confirmation_need'], $resource->user())) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);

        $action = new UserEmailChangeAction($current);
        $action->execute($resource->user(), $vdto);

        return APIResponse::success(
            ($data['email_confirmation_need'] ?? false) && !empty($data['email']) ? 'Запрос на подтверждение адреса электронной почты отправлен' : 'Адрес электронной почты сохранён'
        );
    }
}
