<?php
declare(strict_types=1);

namespace App\Dictionaries;

use App\Dictionaries\Base\EloquentDictionary;
use App\Models\Positions\PositionType;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserDictionary extends EloquentDictionary
{
    protected static string $dictionaryClass = User::class;

    protected static string $title = 'Учётные записи';

    protected static bool|array $viewPermissions = [PositionType::admin => true, PositionType::staff => ['system.staff.change']];

    /**
     * The query for dictionary view.
     *
     * @return  Builder
     */
    public static function query(): Builder
    {
        return User::query()
            ->with(['status'])
            ->select([
                'users.id',
                DB::raw('CONCAT_WS(\' \', users.lastname, users.firstname, users.patronymic) as name'),
                DB::raw('IF(users.status_id = ' . UserStatus::active . ', true, false) as enabled'),
                DB::raw('CONCAT(users.username, " — ", users.email, " — ", users.phone) as hint'),
                'users.lastname as order',
                'users.created_at as created_at',
                'users.updated_at as updated_at',
                'users.lastname',
                'users.firstname',
                'users.patronymic',
                'users.username',
                'users.email',
                'users.phone',
                'users.display_name',
                'users.status_id',
                DB::raw('IF(ISNULL(users.password), false, true) as has_password'),
            ])->distinct();
    }

    /**
     * Format output record.
     *
     * @param User|Model $user
     *
     * @return  array
     */
    public static function asArray(User|Model $user): array
    {
        /** @var UserStatus $status */
        $status = $user->getRelation('status');

        return [
            'id' => $user->id,
            'name' => $user->getAttribute('name'),
            'hint' => $user->getAttribute('hint'),
            'enabled' => (bool)$user->getAttribute('enabled'),
            'info' => [
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'lastname' => $user->lastname,
                'firstname' => $user->firstname,
                'patronymic' => $user->patronymic,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'has_password' => (bool)$user->getAttribute('has_password'),
                'display_name' => $user->display_name,
                'is_active' => $user->getAttribute('enabled'),
                'status' => $status->name,
            ],
        ];
    }
}
