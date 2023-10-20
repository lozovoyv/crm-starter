<?php
declare(strict_types=1);

namespace Database\Seeders\Seeders;

use App\Models\History\HistoryAction;
use Database\Seeders\GenericSeeder;

class HistoryActionsSeeder extends GenericSeeder
{
    protected array $data = [
        HistoryAction::class => [
            /** DICTIONARIES */
            HistoryAction::dictionary_item_created => ['name' => 'Запись справочника :entry создана'],
            HistoryAction::dictionary_item_edited => ['name' => 'Запись справочника :entry изменена'],
            HistoryAction::dictionary_item_deleted => ['name' => 'Запись справочника :entry удалена'],
            HistoryAction::dictionary_item_activated => ['name' => 'Запись справочника :entry включена'],
            HistoryAction::dictionary_item_deactivated => ['name' => 'Запись справочника :entry отключена'],

            /** PERMISSIONS AND ROLES */
            HistoryAction::permission_group_created => ['name' => 'Группа прав ":entry" создана'],
            HistoryAction::permission_group_edited => ['name' => 'Группа прав ":entry" изменена'],
            HistoryAction::permission_group_deleted => ['name' => 'Группа прав ":entry" удалена'],
            HistoryAction::permission_group_activated => ['name' => 'Группа прав ":entry" включена'],
            HistoryAction::permission_group_deactivated => ['name' => 'Группа прав ":entry" отключена'],

            /** USERS */
            HistoryAction::user_created => ['name' => 'Учётная запись :entry создана'],
            HistoryAction::user_edited => ['name' => 'Учётная запись :entry изменена'],
            HistoryAction::user_password_set => ['name' => 'Пароль учётной записи :entry задан'],
            HistoryAction::user_password_changed => ['name' => 'Пароль учётной записи :entry изменён'],
            HistoryAction::user_password_cleared => ['name' => 'Пароль учётной записи :entry удалён'],
            HistoryAction::user_deleted => ['name' => 'Учётная запись :entry удалена'],
            HistoryAction::user_activated => ['name' => 'Учётная запись :entry активирована'],
            HistoryAction::user_deactivated => ['name' => 'Учётная запись :entry заблокирована'],
            HistoryAction::user_staff_attached => ['name' => 'Учётная запись :entry привязана к сотруднику'],
            HistoryAction::user_email_set => ['name' => 'Задан адрес электронной почты для учётной записи :entry'],
            HistoryAction::user_email_changed => ['name' => 'Изменён адрес электронной почты для учётной записи :entry'],
            HistoryAction::user_email_cleared => ['name' => 'Удалён адрес электронной почты для учётной записи :entry'],
            HistoryAction::user_email_verification_sent => ['name' => 'Отправлен запрос на подтверждение адреса электронной почты для учётной записи :entry'],
            HistoryAction::user_email_verified => ['name' => 'Адрес электронной почты для учётной записи :entry подтверждён'],

            /** STAFF */
            HistoryAction::staff_position_created => ['name' => 'Добавлен сотрудник :entry'],
            HistoryAction::staff_position_edited => ['name' => 'Изменены данные сотрудника :entry'],
            HistoryAction::staff_position_deleted => ['name' => 'Сотрудник :entry удалён'],
            HistoryAction::staff_position_activated => ['name' => 'Сотрудник :entry активирован'],
            HistoryAction::staff_position_deactivated => ['name' => 'Сотрудник :entry заблокирован'],
        ],
    ];
}
