<?php
declare(strict_types=1);

namespace App\Dictionaries;

use App\Models\Users\User;
use App\Models\Users\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserDictionary extends Dictionary
{
    protected static string $dictionaryClass = User::class;

    protected static string $name = 'Учётные записи';

    /**
     * The query for dictionary view.
     *
     * @return  Builder
     */
    protected static function query(): Builder
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
     * @param User|Model $model
     *
     * @return  array
     */
    protected static function asArray(User|Model $model): array
    {
        /** @var UserStatus $status */
        $status = $model->getRelation('status');

        return [
            'id' => $model->getAttribute('id'),
            'name' => $model->getAttribute('name'),
            'hint' => implode(
                ' — ', array_filter([
                    'email' => $model->getAttribute('email'),
                    'phone' => $model->getAttribute('phone'),
                    'username' => $model->getAttribute('username'),
                ])
            ),
            'enabled' => (bool)$model->getAttribute('enabled'),
            'info' => [
                'created_at' => $model->getAttribute('created_at'),
                'updated_at' => $model->getAttribute('updated_at'),
                'lastname' => $model->getAttribute('lastname'),
                'firstname' => $model->getAttribute('firstname'),
                'patronymic' => $model->getAttribute('patronymic'),
                'username' => $model->getAttribute('username'),
                'email' => $model->getAttribute('email'),
                'phone' => $model->getAttribute('phone'),
                'has_password' => (bool)$model->getAttribute('has_password'),
                'display_name' => $model->getAttribute('display_name'),
                'is_active' => $model->getAttribute('status_id') === UserStatus::active,
                'status' => $status->name,
            ],
        ];
    }
}
