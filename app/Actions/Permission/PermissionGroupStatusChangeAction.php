<?php
declare(strict_types=1);
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Actions\Permission;

use App\Actions\Action;
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
     */
    public function execute(PermissionGroup $group, PermissionGroupVDTO $vdto): void
    {
        $group->active = $vdto->active;
        $group->save();

        $action = $group->active ? HistoryAction::permission_group_activated : HistoryAction::permission_group_deactivated;
        $group->addHistory($action, $this->current?->positionId());
    }
}
