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

use App\HistoryFormatters\PermissionGroupChangesFormatter;
use App\HistoryFormatters\PositionChangesFormatter;
use App\HistoryFormatters\UserChangesFormatter;
use App\Models\Permissions\PermissionGroup;
use App\Models\Positions\Position;
use App\Models\Users\User;

return [
    'formatters' => [
        PermissionGroup::class => PermissionGroupChangesFormatter::class,
        Position::class => PositionChangesFormatter::class,
        User::class => UserChangesFormatter::class,
    ]
];
