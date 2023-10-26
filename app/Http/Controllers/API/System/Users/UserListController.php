<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\System\Users;

use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use App\Utils\Translate;

class UserListController extends ApiController
{
    protected array $titles = [
        'id' => 'users/user.id',
        'name' => 'users/user.name',
        'email' => 'users/user.email',
        'phone' => 'users/user.phone',
        'created_at' => 'users/user.created_at',
        'updated_at' => 'users/user.updated_at',
    ];

    protected array $orderableColumns = ['id', 'name', 'email', 'phone', 'created_at', 'updated_at'];

    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            PositionType::middleware(PositionType::admin, PositionType::staff),
            Permission::middleware(Permission::system__users, Permission::system__users_change),
        ]);
    }

    /**
     * Get users list.
     *
     * @param APIListRequest $request
     *
     * @return  ApiResponse
     */
    public function __invoke(APIListRequest $request): ApiResponse
    {
        $users = User::query()
            ->filter($request->filters())
            ->search($request->search())
            ->order($request->orderBy('name'), $request->orderDirection('asc'))
            ->pagination($request->page(), $request->perPage());

        $users->transform(function (User $user) {
            return [
                'id' => $user->id,
                'is_active' => $user->hasStatus(UserStatus::active),
                'locked' => $user->locked,
                'status' => $user->status->name,
                'lastname' => $user->lastname,
                'firstname' => $user->firstname,
                'patronymic' => $user->patronymic,
                'display_name' => $user->display_name,
                'username' => $user->username,
                'email' => $user->email,
                'has_password' => !empty($user->password),
                'phone' => $user->phone,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'hash' => $user->hash(),
            ];
        });

        return ApiResponse::list($users)
            ->titles(Translate::array($this->titles))
            ->orderable($this->orderableColumns);
    }
}
