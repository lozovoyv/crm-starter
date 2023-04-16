<?php
declare(strict_types=1);

namespace App\Dictionaries;

use App\Models\Permissions\PermissionGroup;
use Illuminate\Database\Eloquent\Model;

class PermissionRoleDictionary extends Dictionary
{
    protected static string $dictionaryClass = PermissionGroup::class;

    protected static string $name = 'Роли';
    protected static bool $orderable = false;

    protected static string $id_field = 'id';
    protected static string $name_field = 'name';
    protected static ?string $hint_field = 'description';
    protected static ?string $enabled_field = 'active';
    protected static ?string $order_field = 'name';
    protected static ?string $updated_at_field = 'updated_at';
    protected static ?string $locked_field = 'locked';

    /**
     * Format output record.
     *
     * @param PermissionGroup|Model $model
     *
     * @return  array
     */
    protected static function asArray(PermissionGroup|Model $model): array
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
