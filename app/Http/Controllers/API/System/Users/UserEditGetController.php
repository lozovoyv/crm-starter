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

use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
use App\Resources\Users\UserResource;
use App\VDTO\UserVDTO;
use Exception;

class UserEditGetController extends ApiController
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
     * Get user data.
     *
     * @param int|null $userID
     *
     * @return ApiResponse
     */
    public function __invoke(?int $userID = null): ApiResponse
    {

        /** @var User $user */
        try {
            $resource = UserResource::init($userID, null, false, false);
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        $vdto = new UserVDTO();

        $fields = [
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
        ];

        return ApiResponse::form()
            ->title($user->exists ? $user->fullName : 'Создание учётной записи')
            ->values($resource->values($fields))
            ->rules($vdto->getValidationRules($fields))
            ->titles($vdto->getTitles($fields))
            ->messages($vdto->getValidationMessages($fields))
            ->hash($resource->getHash($user))
            ->payload(['has_password' => !empty($user->password)]);
    }
}
