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
use App\Models\History\HistoryChanges;
use App\Models\Permissions\PermissionGroup;
use App\Utils\Casting;
use App\VDTO\PermissionGroupVDTO;

class PermissionGroupUpdateAction extends Action
{
    /**
     * Update user data.
     *
     * @param PermissionGroup $group
     * @param PermissionGroupVDTO $vdto
     *
     * @return void
     */
    public function execute(PermissionGroup $group, PermissionGroupVDTO $vdto): void
    {
        $changes = [];

        $changes[] = $group->setAttributeWithChanges('name', $vdto->name, Casting::string);
        $changes[] = $group->setAttributeWithChanges('active', $vdto->active, Casting::bool);
        $changes[] = $group->setAttributeWithChanges('description', $vdto->description, Casting::string);
        $group->save();

        $ids = array_keys(array_filter($vdto->permission));
        $oldIds = $group->permissions()->pluck('id')->toArray();

        sort($ids);
        sort($oldIds);

        $changed = $group->permissions()->sync($ids);

        if (count($changed['attached']) || count($changed['updated']) || count($changed['detached'])) {
            $group->touch();
            $changes[] = new HistoryChanges(['parameter' => 'permissions', 'type' => Casting::array, 'old' => $oldIds, 'new' => $ids]);
        }

        $changes = array_filter($changes);

        if (!empty($changes)) {
            $group
                ->addHistory($group->wasRecentlyCreated ? HistoryAction::permission_group_created : HistoryAction::permission_group_edited, $this->current?->positionId())
                ->addChanges($changes);
        }
    }
}
