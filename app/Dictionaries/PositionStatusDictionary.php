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

namespace App\Dictionaries;

use App\Dictionaries\Base\EloquentDictionary;
use App\Models\Positions\PositionStatus;
use App\Models\Positions\PositionType;

class PositionStatusDictionary extends EloquentDictionary
{
    protected static string $dictionaryClass = PositionStatus::class;

    protected static string $title = 'dictionaries/position_statuses.title';

    public static bool|array $viewPermissions = [PositionType::admin => true, PositionType::staff => ['system.staff', 'system.staff.change']];
    protected static ?string $order_field = 'name';
    protected static ?string $locked_field = null;
}
