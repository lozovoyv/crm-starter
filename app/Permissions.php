<?php
declare(strict_types=1);
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

class Permissions
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

    public static function middleware(...$permissions): string
    {
        return 'permission:' . implode(',', $permissions);
    }
}
