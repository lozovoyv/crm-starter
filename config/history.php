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

use App\HistoryFormatters\PermissionGroupChangeFormatter;
use App\HistoryFormatters\PositionChangeFormatter;
use App\HistoryFormatters\UserChangeFormatter;
use App\Models\Permissions\PermissionGroup;
use App\Models\Positions\Position;
use App\Models\Users\User;

return [
    'formatters' => [
        PermissionGroup::class => PermissionGroupChangeFormatter::class,
        Position::class => PositionChangeFormatter::class,
        User::class => UserChangeFormatter::class,
    ]
];
