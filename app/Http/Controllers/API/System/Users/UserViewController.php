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

use App\Exceptions\Model\ModelNotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Models\Users\UserStatus;
use App\Resources\Users\UserResource;
use Exception;

class UserViewController extends ApiController
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__users),
        ]);
    }

    /**
     * User view.
     *
     * @param int $userID
     *
     * @return ApiResponse
     */
    public function __invoke(int $userID): ApiResponse
    {
        try {
            $resource = UserResource::get($userID);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound($exception->getMessage());
        } catch (Exception $exception) {
            return ApiResponse::error($exception->getMessage());
        }

        $user = $resource->user();

        $data = [
            'id' => $user->id,
            'locked' => $user->locked,
            'lastname' => $user->lastname,
            'firstname' => $user->firstname,
            'patronymic' => $user->patronymic,
            'username' => $user->username,
            'display_name' => $user->display_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'is_active' => $user->hasStatus(UserStatus::active),
            'status' => $user->status->name,
            'has_password' => !empty($user->password),
            'name' => $user->compactName,
            'hash' => $user->hash(),
        ];

        return APIResponse::common($data);
    }
}
