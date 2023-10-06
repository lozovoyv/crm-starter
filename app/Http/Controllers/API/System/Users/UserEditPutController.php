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
use App\Actions\Users\UserEmailChangeAction;
use App\Actions\Users\UserStatusChangeAction;
use App\Actions\Users\UserUpdateAction;
use App\Current;
use App\Exceptions\Model\ModelException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Resources\Users\UserResource;
use App\VDTO\UserVDTO;

class UserEditPutController extends ApiController
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
     * Update user data.
     *
     * @param APIRequest $request
     * @param int|null $userID
     *
     * @return  ApiResponse
     */
    public function __invoke(APIRequest $request, ?int $userID = null): ApiResponse
    {

        try {
            $resource = UserResource::init($userID, $request->hash(), true, false);
        } catch (ModelException $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $vdto = new UserVDTO(
            $request->data([
                'lastname',
                'firstname',
                'patronymic',
                'display_name',
                'username',
                'phone',
                'status_id',
                'new_password',
                'clear_password',
                'email',
                'email_confirmation_need',
            ])
        );

        if ($errors = $vdto->validate([], $resource->user())) {
            return APIResponse::validationError($errors);
        }

        $current = Current::init($request);
        $user = $resource->user();

        $action = new UserUpdateAction($current);
        $action->execute($user, $vdto);

        $action = new UserStatusChangeAction($current);
        $action->execute($user, $vdto);

        $action = new UserPasswordChangeAction($current);
        $action->execute($user, $data['new_password'] ?? null, $data['clear_password'] ?? false);

        $action = new UserEmailChangeAction($current);
        $action->execute($user, $data['email'] ?? null, $data['email_confirmation_need'] ?? false);

        return APIResponse::success()
            ->message($user->wasRecentlyCreated ? 'Учётная запись добавлена' : 'Учётная запись сохранена')
            ->payload(['id' => $user->id]);
    }
}
