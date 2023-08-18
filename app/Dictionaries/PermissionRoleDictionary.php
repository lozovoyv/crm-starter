<?php
declare(strict_types=1);

namespace App\Dictionaries;

use App\Dictionaries\Base\EloquentDictionary;
use App\Models\Permissions\PermissionGroup;
use App\Models\Positions\PositionType;
use Illuminate\Database\Eloquent\Model;

class PermissionRoleDictionary extends EloquentDictionary
{
    protected static string $dictionaryClass = PermissionGroup::class;

    protected static string $title = 'Роли';

    protected static bool|array $viewPermissions = [PositionType::admin => true, PositionType::staff => ['system.staff', 'system.staff.change']];

    protected static bool $orderable = false;

    protected static ?string $hint_field = 'description';
    protected static ?string $enabled_field = 'active';
    protected static ?string $order_field = 'name';

    /**
     * Format output record.
     *
     * @param PermissionGroup|Model $model
     *
     * @return  array
     */
    public static function asArray(PermissionGroup|Model $model): array
    {
        return [
            'id' => $model->getAttribute('id'),
            'name' => $model->getAttribute('name'),
            'enabled' => $model->getAttribute('enabled'),
            'hint' => $model->getAttribute('hint'),
            'locked' => $model->getAttribute('locked'),
        ];
    }
}
