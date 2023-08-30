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
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use App\Models\Users\User;

class UserDictionary extends EloquentDictionary
{
    protected static string $dictionaryClass = User::class;

    protected static string $title = 'dictionaries/users.title';

    public static bool|array $viewPermissions = [PositionType::admin => true, PositionType::staff => [Permission::system__staff_change]];
}
