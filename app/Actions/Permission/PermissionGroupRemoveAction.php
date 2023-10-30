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

namespace App\Actions\Permission;

use App\Actions\Action;
use App\Exceptions\Model\ModelDeleteBlockedException;
use App\Exceptions\Model\ModelLockedException;
use App\Exceptions\Model\ModelNotFoundException;
use App\Models\History\HistoryAction;
use App\Models\History\HistoryChange;
use App\Models\Permissions\PermissionGroup;
use App\Utils\Casting;
use Illuminate\Database\QueryException;

class PermissionGroupRemoveAction extends Action
{
    /**
     * @throws ModelDeleteBlockedException
     * @throws ModelNotFoundException
     * @throws ModelLockedException
     */
    public function execute(PermissionGroup $group): void
    {
        if (!$group->exists) {
            throw new ModelNotFoundException('permissions/permission_group.model_not_found_exception');
        }
        if ($group->locked) {
            throw new ModelLockedException('permissions/permission_group.model_locked_exception');
        }

        try {
            $changes = [
                new HistoryChange(['parameter' => 'name', 'type' => Casting::string, 'old' => $group->name, 'new' => null]),
                new HistoryChange(['parameter' => 'active', 'type' => Casting::bool, 'old' => $group->active, 'new' => null]),
                new HistoryChange(['parameter' => 'description', 'type' => Casting::string, 'old' => $group->description, 'new' => null]),
                new HistoryChange(['parameter' => 'permissions', 'type' => Casting::array, 'old' => $group->permissions()->pluck('id')->toArray(), 'new' => null]),
            ];

            $group
                ->addHistory(HistoryAction::permission_group_deleted, $this->current?->positionId())
                ->addChanges($changes);

            $group->delete();

            $this->resultMessage = 'permissions/permission_group.group_deleted';

        } catch (QueryException) {
            throw new ModelDeleteBlockedException('permissions/permission_group.model_delete_blocked_exception');
        }
    }
}
