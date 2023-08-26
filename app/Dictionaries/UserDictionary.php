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
use App\Models\Positions\PositionType;
use App\Models\Users\User;
use App\Models\Users\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserDictionary extends EloquentDictionary
{
    protected static string $dictionaryClass = User::class;

    protected static string $title = 'dictionaries/users.title';

    public static bool|array $viewPermissions = [PositionType::admin => true, PositionType::staff => ['system.staff.change']];
}
