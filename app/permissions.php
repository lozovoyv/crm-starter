<?php
declare(strict_types=1);

return [
    'scopes' => [
        'system' => 'Система',
    ],
    'permissions' => [
        //[key, name, description]
        ['system.staff', 'Просмотр сотрудников', 'Просмотр сотрудников.'],
        ['system.staff.change', 'Редактирование сотрудников', 'Добавление, редактирование, удаление сотрудников.'],
        ['system.users', 'Просмотр учётных записей', 'Просмотр всех учётных записей системы.'],
        ['system.users.change', 'Редактирование учётных записей', 'Добавление, редактирование, удаление учётных записей, изменение данных для входа в систему.'],
        ['system.settings', 'Изменение настроек системы', 'Изменение системных настроек.'],
        ['system.dictionaries', 'Редактирование справочников', 'Создание, редактирование, удаление записей в системных справочниках.'],
        ['system.roles', 'Редактирование ролей', 'Создание, редактирование, удаление, настройка ролей.'],
        ['system.history', 'Просмотр журнала операций', 'Просмотр журнала всех операций.'],
        ['system.act_as_other', 'Просмотр системы от лица другого пользователя', 'Просмотр системы и совершение операция от лица другого пользователя.'],
    ],
];
