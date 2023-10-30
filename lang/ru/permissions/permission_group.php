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

return [
    'id' => 'ID',
    'name' => 'Название',
    'count' => 'Права',
    'description' => 'Описание',
    'created_at' => 'Создано',
    'updated_at' => 'Изменено',

    'field_name' => 'Название',
    'field_active' => 'Статус',
    'field_description' => 'Описание',
    'field_permissions' => 'Права',

    'validation_name_unique' => 'Группа прав с таким названием уже существует.',

    'model_not_found_exception' => 'Группа прав не найдена.',
    'model_wrong_hash_exception' => 'Группа прав была изменена в другом месте.',
    'model_locked_exception' => 'Эту группу прав нельзя изменить или удалить.',
    'model_delete_blocked_exception' => 'Невозможно удалить группу прав. Она задействована в системе.',

    'new_group' => 'Создание группы прав',

    'group_deleted' => 'Группа прав удалена.',
    'group_activated' => 'Группа прав включена.',
    'group_deactivated' => 'Группа прав отключена.',
    'group_not_modified' => 'Изменений не сделано.',
    'group_created' => 'Группа прав добавлена.',
    'group_updated' => 'Группа прав сохранена.',
];
