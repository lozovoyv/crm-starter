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
    'name' => 'Name',
    'count' => 'Permissions',
    'description' => 'Description',
    'created_at' => 'Created',
    'updated_at' => 'Updated',

    'field_name' => 'Name',
    'field_active' => 'Status',
    'field_description' => 'Description',
    'field_permissions' => 'Permissions',

    'validation_name_unique' => 'Permission group with same name is already exists.',

    'model_not_found_exception' => 'Permission group is not found.',
    'model_wrong_hash_exception' => 'Permission group was changed in other place.',
    'model_locked_exception' => 'This permission group can not be changed or deleted.',
    'model_delete_blocked_exception' => 'This permission group can not be deleted. It used in system.',

    'new_group' => 'Creation of permission group',

    'group_deleted' => 'Permission group has been deleted.',
    'group_activated' => 'Permission group has been enabled.',
    'group_deactivated' => 'Permission group been disabled.',
    'group_not_modified' => 'No changes made.',
    'group_created' => 'Permission group has been created.',
    'group_updated' => 'Permission group has been created.',
];
