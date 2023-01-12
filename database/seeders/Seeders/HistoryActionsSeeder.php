<?php

namespace Database\Seeders\Seeders;

use App\Models\History\HistoryAction;
use Database\Seeders\GenericSeeder;

class HistoryActionsSeeder extends GenericSeeder
{
    protected array $data = [
        HistoryAction::class => [
            /** PERMISSIONS AND ROLES */
            HistoryAction::permission_role_created => ['name' => 'Роль :entry создана'],
            HistoryAction::permission_role_edited => ['name' => 'Роль :entry изменена'],
            HistoryAction::permission_role_deleted => ['name' => 'Роль :entry удалена'],
            HistoryAction::permission_role_activated => ['name' => 'Роль :entry включена'],
            HistoryAction::permission_role_deactivated => ['name' => 'Роль :entry отключена'],

            /** USERS */
            HistoryAction::user_created => ['name' => 'Учётная запись :entry создана'],
            HistoryAction::user_edited => ['name' => 'Учётная запись :entry изменена'],
            HistoryAction::user_password_set => ['name' => 'Пароль учётной записи :entry задан'],
            HistoryAction::user_password_changed => ['name' => 'Пароль учётной записи :entry изменён'],
            HistoryAction::user_password_cleared => ['name' => 'Пароль учётной записи :entry удалён'],
            HistoryAction::user_deleted => ['name' => 'Учётная запись :entry удалена'],
            HistoryAction::user_activated => ['name' => 'Учётная запись :entry активирована'],
            HistoryAction::user_deactivated => ['name' => 'Учётная запись :entry заблокирована'],

            HistoryAction::staff_position_created => ['name' => 'Добавлен сотрудник :entry'],
        ],
    ];
}
