<?php

namespace App\Models\History\Formatters;

use App\Models\History\HistoryChanges;

class PermissionRoleChangesFormatter implements FormatterInterface
{
    public static function format(HistoryChanges $changes): array
    {
        $result = [
            'parameter' => $changes->parameter,
            'old' => $changes->old,
            'new' => $changes->new,
        ];

        switch ($result['parameter']) {
            case 'name':
                $result['parameter'] = 'Название';
                break;
            case 'active':
                $result['parameter'] = 'Статус';
                $result['old'] = $result['old'] === null ? null : ($result['old'] ? 'Включена' : 'Отключена');
                $result['new'] = $result['new'] === null ? null : ($result['new'] ? 'Включена' : 'Отключена');
                break;
            case 'description':
                $result['parameter'] = 'Описание';
                break;
            case 'permissions':
                $result['parameter'] = 'Права';
                break;
        }

        return $result;
    }
}
