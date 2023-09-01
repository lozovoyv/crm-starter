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

use App\Models\History\HistoryChanges;

class PermissionGroupChangesFormatter implements FormatterInterface
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
                break;
        }

        return $result;
    }
}
