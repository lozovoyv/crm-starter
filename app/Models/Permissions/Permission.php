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

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $key
 * @property string $scope_name
 * @property string $name
 * @property string|null $description
 * @property int $order
 *
 * @property PermissionScope $scope
 * @property Collection<PermissionGroup> $groups
 */
class Permission extends Model
{
    public const system__permissions = 'system__permissions';
    public const system__staff = 'system__staff';
    public const system__staff_change = 'system__staff_change';
    public const system__users = 'system__users';
    public const system__users_change = 'system__users_change';
    public const system__settings = 'system__settings';
    public const system__dictionaries = 'system__dictionaries';
    public const system__history = 'system__history';
    public const system__act_as_other = 'system__act_as_other';


    public static array $scopes = [
        'system' => 'Система',
    ];

    //key => [name, description]
    public static array $permissions = [
        self::system__permissions => [
            'name' => 'Редактирование прав',
            'description' => 'Настройка прав, создание, редактирование, удаление групп прав.',
        ],
        self::system__staff => [
            'name' => 'Просмотр сотрудников',
            'description' => 'Просмотр сотрудников.',
        ],
        self::system__staff_change => [
            'name' => 'Редактирование сотрудников',
            'description' => 'Добавление, редактирование, удаление сотрудников.',
        ],
        self::system__users => [
            'name' => 'Просмотр учётных записей',
            'description' => 'Просмотр всех учётных записей системы.',
        ],
        self::system__users_change => [
            'name' => 'Редактирование учётных записей',
            'description' => 'Добавление, редактирование, удаление учётных записей, изменение данных для входа в систему.',
        ],
        self::system__settings => [
            'name' => 'Изменение настроек системы',
            'description' => 'Изменение системных настроек.',
        ],
        self::system__dictionaries => [
            'name' => 'Редактор справочников',
            'description' => 'Создание, редактирование, удаление записей в справочниках.',
        ],
        self::system__history => [
            'name' => 'Просмотр журнала операций',
            'description' => 'Просмотр журнала всех операций.',
        ],
        self::system__act_as_other => [
            'name' => 'Просмотр системы от лица другого пользователя',
            'description' => 'Просмотр системы и совершение операция от лица другого пользователя.',
        ],
    ];

    protected $fillable = [
        'key',
        'scope_name',
        'name',
    ];

    /**
     * Get permission by key.
     *
     * @param string $key
     *
     * @return Permission|null
     */
    public static function get(string $key): ?Permission
    {
        /** @var Permission|null $permission */
        $permission = self::query()->where('key', $key)->first();

        return $permission;
    }

    /**
     * Permission's module.
     *
     * @return  BelongsTo
     */
    public function scope(): BelongsTo
    {
        return $this->belongsTo(PermissionScope::class, 'scope_name', 'scope_name');
    }

    /**
     * Groups this permission attached to.
     *
     * @return  BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(PermissionGroup::class, 'permission_in_group', 'permission_id', 'group_id');
    }

    /**
     * Make permissions middleware.
     *
     * @param string ...$permissions
     *
     * @return string
     */
    public static function middleware(...$permissions): string
    {
        return 'permission:' . implode(',', $permissions);
    }
}
