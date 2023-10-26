<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\HistoryFormatters;

use App\Models\History\HistoryChange;
use App\Models\Permissions\Permission;

class PermissionGroupChangeFormatter implements FormatterInterface
{
    public static function format(HistoryChange $changes): array
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
                if ($result['old'] !== null) {
                    $result['old'] = $result['old'] ? 'Включена' : 'Отключена';
                }
                if ($result['new'] !== null) {
                    $result['new'] = $result['new'] ? 'Включена' : 'Отключена';
                }
                break;
            case 'description':
                $result['parameter'] = 'Описание';
                break;
            case 'permissions':
                $result['parameter'] = 'Права';
                [$result['old'], $result['new']] = self::formatPermissionChange($result['old'] ?? [], $result['new'] ?? []);
                break;
        }

        return $result;
    }

    protected static function formatPermissionChange($old, $new): array
    {
        $removed = array_diff($old, $new);
        $added = array_diff($new, $old);
        $changes = array_merge($removed, $added);

        $oldValues = empty($old) ? '—' : Permission::query()->whereIn('id', $old)->withScope()->get()->map(function (Permission $permission) {
            return $permission->scope->name . ': ' . mb_strtolower($permission->name);
        })->sort()->values()->toArray();

        $newValues = empty($changes) ? '—' : Permission::query()->whereIn('id', $changes)->withScope()->get()->map(
            function (Permission $permission) use ($removed) {
                $sign = in_array($permission->id, $removed, true) ? '-' : '+';
                return $sign . ' ' . $permission->scope->name . ': ' . mb_strtolower($permission->name);
            }
        )->sort()->values()->toArray();

        return [
            $oldValues,
            $newValues,
        ];
    }
}
