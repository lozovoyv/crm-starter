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

namespace Tests\Traits;

use App\Models\Permissions\Permission;
use App\Models\Positions\Position;
use App\Models\Positions\PositionStatus;
use App\Models\Users\User;

trait CreatesPosition
{
    /**
     * Creates the position.
     *
     * @param User $user
     * @param int $type
     * @param array|null $permissions
     *
     * @return Position
     */
    public function createPosition(User $user, int $type, ?array $permissions = null): Position
    {
        $position = new Position();
        $position->type_id = $type;
        $position->user_id = $user->id;
        $position->status_id = PositionStatus::active;
        $position->save();

        if ($permissions !== null) {
            $permissions = Permission::query()->whereIn('key', $permissions)->pluck('id')->toArray();
            $position->permissions()->sync($permissions);
        }

        return $position;
    }
}
