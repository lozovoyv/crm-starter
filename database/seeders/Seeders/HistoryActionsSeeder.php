<?php

namespace Database\Seeders\Seeders;

use App\Models\History\HistoryAction;
use Database\Seeders\GenericSeeder;

class HistoryActionsSeeder extends GenericSeeder
{
    protected array $data = [
        HistoryAction::class => [
            /** PERMISSIONS ADN ROLES */
            HistoryAction::permission_role_created => ['name' => 'Роль ":entry" создана'],
            HistoryAction::permission_role_edited => ['name' => 'Роль ":entry" изменена'],
            HistoryAction::permission_role_deleted => ['name' => 'Роль ":entry" удалена'],
            HistoryAction::permission_role_activated => ['name' => 'Роль ":entry" включена'],
            HistoryAction::permission_role_deactivated => ['name' => 'Роль ":entry" отключена'],

            HistoryAction::user_created => ['name' => 'Добавлена учётная запись :entry'],

            HistoryAction::staff_position_created => ['name' => 'Добавлен сотрудник :entry'],
        ],
    ];
}
