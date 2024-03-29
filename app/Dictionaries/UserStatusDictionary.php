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

namespace App\Dictionaries;

use App\Dictionaries\Base\EloquentDictionary;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Models\Users\UserStatus;
use Illuminate\Database\Eloquent\Model;

class UserStatusDictionary extends EloquentDictionary
{
    protected static string $dictionaryModel = UserStatus::class;

    protected static string $title = 'dictionaries/user_statuses.title';

    public static bool|array $viewPermissions = [PositionType::admin => true, PositionType::staff => [Permission::system__users, Permission::system__users_change]];

    protected static ?string $order_field = 'name';
    protected static ?string $locked_field = null;
    protected static ?string $enabled_field = null;

    public static function asArray(UserStatus|Model $model): array
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
        ];
    }
}
