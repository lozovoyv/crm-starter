<?php

namespace App\Models\History\Formatters;

use App\Models\History\HistoryChanges;
use App\Models\Permissions\PermissionRole;
use App\Models\Positions\PositionStatus;
use App\Models\Users\User;

class PositionChangesFormatter implements FormatterInterface
{
    public static function format(HistoryChanges $changes): array
    {
        $result = [
            'parameter' => $changes->parameter,
            'old' => $changes->old,
            'new' => $changes->new,
        ];

        switch ($result['parameter']) {
            case 'user_id':
                $result['parameter'] = 'Учётная запись';
                /** @var User $user */
                $user = $result['old'] === null ? null : User::query()->where('id', $result['old'])->first();
                $result['old'] = $user?->compactName;
                /** @var User $user */
                $user = $result['new'] === null ? null : User::query()->where('id', $result['new'])->first();
                $result['new'] = $user?->compactName;
                break;
            case 'status_id':
                $result['parameter'] = 'Статус';
                $result['old'] = $result['old'] === null ? null : PositionStatus::query()->where('id', $result['old'])->value('old');
                $result['new'] = $result['new'] === null ? null : PositionStatus::query()->where('id', $result['new'])->value('name');
                break;
            case 'roles':
                $result['parameter'] = 'Роли';
                $result['old'] = $result['old'] === null ? null : PermissionRole::query()->whereIn('id', $result['old'])->pluck('name')->toArray();
                $result['new'] = $result['new'] === null ? null : PermissionRole::query()->whereIn('id', $result['new'])->pluck('name')->toArray();
                break;
        }

        return $result;
    }
}