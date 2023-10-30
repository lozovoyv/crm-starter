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
use App\Exceptions\Model\ModelLockedException;
use App\Exceptions\Model\ModelNotFoundException;
use App\Models\History\HistoryAction;
use App\Models\Permissions\PermissionGroup;
use App\VDTO\PermissionGroupVDTO;

class PermissionGroupStatusChangeAction extends Action
{
    /**
     * Change user status.
     *
     * @param PermissionGroup $group
     * @param PermissionGroupVDTO $vdto
     *
     * @return void
     * @throws ModelNotFoundException
     * @throws ModelLockedException
     */
    public function execute(PermissionGroup $group, PermissionGroupVDTO $vdto): void
    {
        if (!$group->exists) {
            throw new ModelNotFoundException('permissions/permission_group.model_not_found_exception');
        }
        if ($group->locked) {
            throw new ModelLockedException('permissions/permission_group.model_locked_exception');
        }

        $group->active = $vdto->active;
        $group->save();

        $action = $group->active ? HistoryAction::permission_group_activated : HistoryAction::permission_group_deactivated;
        $group->addHistory($action, $this->current?->positionId());

        $this->resultMessage = $group->active ? 'permissions/permission_group.group_activated' : 'permissions/permission_group.group_deactivated';
    }
}
