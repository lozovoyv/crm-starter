<?php
declare(strict_types=1);

namespace App\Dictionaries;

use App\Dictionaries\Base\EloquentDictionary;
use App\Models\Positions\PositionType;
use App\Models\Users\UserStatus;
use Illuminate\Database\Eloquent\Model;

class UserStatusDictionary extends EloquentDictionary
{
    protected static string $dictionaryClass = UserStatus::class;

    protected static string $title = 'Статус учётной записи';

    public static bool|array $viewPermissions = [PositionType::admin => true, PositionType::staff => ['system.users', 'system.users.change']];

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
