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
use App\Exceptions\Model\ModelDeleteBlockedException;
use App\Models\History\HistoryAction;
use App\Models\History\HistoryChange;
use App\Models\Permissions\PermissionGroup;
use App\Utils\Casting;
use Illuminate\Database\QueryException;

class PermissionGroupRemoveAction extends Action
{
    /**
     * @throws ModelDeleteBlockedException
     */
    public function execute(PermissionGroup $group): void
    {
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

        } catch (QueryException) {
            throw new ModelDeleteBlockedException('Невозможно удалить группу прав. Она задействована в системе');
        }
    }
}
