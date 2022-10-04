<?php

namespace Database\Seeders\Seeders;

use App\Models\History\HistoryAction;
use Database\Seeders\GenericSeeder;

class HistoryActionsSeeder extends GenericSeeder
{
    protected array $data = [
        HistoryAction::class => [
            /** PERMISSIONS ADN ROLES */
            HistoryAction::permission_role_created => ['name' => 'Роль создана'],
            HistoryAction::permission_role_edited => ['name' => 'Роль изменена'],
            HistoryAction::permission_role_deleted => ['name' => 'Роль удалена'],
            HistoryAction::permission_role_activated => ['name' => 'Роль включена'],
            HistoryAction::permission_role_deactivated => ['name' => 'Роль отключена'],
        ],
    ];
}
